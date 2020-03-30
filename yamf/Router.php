<?php

namespace Yamf;

use Exception;
use PDO;

use Yamf\Util;
use Yamf\AppConfig;

use Yamf\Responses\ErrorMessage;
use Yamf\Responses\NotFound;
use Yamf\Responses\Response;
use Yamf\Request;

class Router
{
    public function route(AppConfig $app, array $routes)
    {
        $request = $_SERVER['REQUEST_URI'];
        
        if (Util::strEndsWith($request, '.php')) {
            // redirect to non-php file -- only works if file doesn't actually exist
            $request = substr($request, 0, -4);
            header("Location: $request");
            die();
        }

        // replace first occurance of $app->basePath in $request
        $pos = strpos($request, $app->basePath);
        if ($pos !== false) {
            $requestURL = substr_replace($request, '', $pos, strlen($app->basePath));
        } else {
            $requestURL = $request;
        }

        $request = $this->findRoute($routes, $requestURL);
        if ($request !== null) {
            $controller = new $request->controller;
            try {
                $data = $controller->{$request->function}($app, $request);
                if ($data != null) {
                    /** @var Response $data */
                    $data->output($app);
                }
            } catch (\Exception $e) {
                $this->showErrorOnException($app, $e);
            }
        } else {
            // see if there is a static URL or shortened URL
            // get the final path of the URL
            $path = parse_url($requestURL, PHP_URL_PATH);
            if ($path != null && $path !== '') {
                $pathParts = explode('/', $path);
                Util::removeEmptyStringsFromArray($pathParts);
                // ok, the desired path is in the final section
                $pathCount = count($pathParts);
                if ($pathCount > 0) {
                    $fixedPath = implode('/', $pathParts);
                    $potentialFileName = $app->staticViewsFolderName . $fixedPath . $app->staticViewExtension;
                    if (file_exists($potentialFileName)) {
                        $name = $pathParts[$pathCount - 1];
                        $nameParts = explode('-', $name);
                        for ($i = 0; $i < count($nameParts); $i++) {
                            $nameParts[$i] = ucfirst($nameParts[$i]);
                        }
                        $title = implode(' ', $nameParts);
                        if ($app->staticPageHeaderName != null) {
                            require_once $app->viewsFolderName . $app->staticPageHeaderName . $app->staticViewExtension;
                        }
                        require_once $potentialFileName;
                        if ($app->staticPageFooterName != null) {
                            require_once $app->viewsFolderName . $app->staticPageFooterName . $app->staticViewExtension;
                        }
                        die();
                    } elseif ($app->isShortURLEnabled && isset($app->db)) {
                        // see if route is a shortened URL since it isn't a static page
                        $potentialRoute = $this->loadShortenedURL($fixedPath, $app->db);
                        if ($potentialRoute !== null && $potentialRoute != "") {
                            header("Location: $potentialRoute");
                            die();
                        }
                    }
                }
            }
            // couldn't determine route
            $this->showNotFound($app);
        }
    }

    /**
     * Shows the 404 not found page
     * 
     * @param AppConfig $app
     */
    public function showNotFound(AppConfig $app) : void
    {
        $notFound = new NotFound();
        $notFound->output($app);
    }

    /**
     * Shows an error on routing exception
     * 
     * @param AppConfig $app
     * @param Exception $e
     */
    public function showErrorOnException(AppConfig $app, Exception $e) : void
    {
        if (isset($app->shouldShowErrorOnExceptionThrown)) {
            if ($app->shouldShowErrorOnExceptionThrown) {
                $response = new ErrorMessage($e->getMessage());
                $response->statusCode = 500;
            }
            else {
                $response = new Response(500);
            }
            $response->output($app);
        }
    }

    /**
     * Attempts to find a route (as defined in routes.php) that works for
     * the given request string.
     * Returns null if it didn't find a route.
     *
     * @param array $routes
     * @param string $request
     * @return null|Request
     */
    function findRoute(array $routes, string $request)
    {
        // Need to make sure # and ? in URL are handled properly and one doesn't interfere with the other!
        $anchorOnPage = parse_url($request, PHP_URL_FRAGMENT); // http://.../blog#comments (#comments)
        if ($anchorOnPage !== null) {
            // remove the anchor from the request so we handle everything properly
            $anchorLocation = strrpos($request, "#"); // strrpos = searches backwards
            $request = substr($request, 0, $anchorLocation);
        }

        // pull out get parameters if there are any
        $getParamLocation = strpos($request, '?');
        $getParams = [];
        if ($getParamLocation !== false) {
            // parse out GET params
            if ($getParamLocation + 1 < strlen($request)) {
                $getStr = substr($request, $getParamLocation + 1);
                parse_str($getStr, $getParams); // getParams now has the get parameters
            }
            $request = substr($request, 0, $getParamLocation);
        }

        // now parse the actual request and find its route
        $requestParts = explode('/', $request);
        Util::removeEmptyStringsFromArray($requestParts);
        $numberOfRequestParts = count($requestParts);
        $requestMethod = Util::getRequestMethod();
        // find the route
        foreach ($routes as $route => $path) {
            $routeParts = explode('/', $route);
            // remove empty request parts for ease of figuring out where we are
            Util::removeEmptyStringsFromArray($routeParts);
            // a matching route will have the same number of parts
            if (count($routeParts) !== $numberOfRequestParts) {
                continue;
            }
            // make sure GET or POST matches up
            // first check to see if they defined multiple routes for a single route (e.g. GET and POST)
            if (count($path) > 0 && is_array($path[0])) {
                // potentially multiple GET/POST routes defined for this route
                foreach ($path as $potentialPath) {
                    if (count($potentialPath) === 3) { // safety
                        if (strtolower($potentialPath[0]) === strtolower($requestMethod)) {
                            $path = $potentialPath;
                            break;
                        }
                    }
                }
            } elseif (count($path) == 2 && !Util::isGetRequest()) {
                continue; // didn't match up as the route is a GET route
            } elseif (count($path) === 3) {
                if (strtolower($path[0]) !== strtolower($requestMethod)) {
                    continue;
                }
            }

            $didFind = true;
            $foundParams = [];
            // ok, we have the same number of parts in the url; now see if the url text lines up
            for ($i = 0; $i < $numberOfRequestParts; $i++) {
                $matches = [];
                // we allow for "parameters" in the url as defined by { } in the route definition
                // not to be confused with GET params, which come after the ? part of the URL!
                // regex from https://stackoverflow.com/a/413165/3938401
                $routeParseResult = preg_match("/\{([^}]+)\}/", $routeParts[$i], $matches);
                if ($routeParseResult !== 0 && $routeParseResult !== false) {
                    // found a match! match will be in array location 1
                    $foundParams[$matches[1]] = $requestParts[$i];
                } elseif ($requestParts[$i] !== $routeParts[$i]) {
                    $didFind = false;
                    break;
                }
            }
            // found it? return the output!
            if ($didFind) {
                $output = new Request();
                $output->route = $route;
                if (count($path) == 2) {
                    $output->controller = '\Controllers\\' . $path[0];
                    $output->function = $path[1];
                } elseif (count($path) == 3) {
                    $output->controller = '\Controllers\\' . $path[1];
                    $output->function = $path[2];
                }

                // since we use PSR-4 and need \ for the "path" to controllers,
                // we make it easy on users and let them use Parent-Folder/Controller-Name
                // OR Parent-Folder\\Controller-Name in routes.php.
                if (Util::strStartsWith($output->controller, '\App') === false &&
                    Util::strStartsWith($output->controller, 'App\\') === false &&
                    Util::strStartsWith($output->controller, 'App/') === false) {
                    $output->controller = '\App' . $output->controller;
                }
                $output->controller = str_replace('/', '\\', $output->controller);
                $output->routeParams = $foundParams;
                $output->get = $getParams;
                $output->anchor = $anchorOnPage;
                $output->post = $_POST;
                return $output;
            }
        }
        return null;
    }

    /**
     * Returns NULL if no shorter URL found; destination as string if found
     * @param string $url
     * @param PDO $db
     * @return string
     */
    function loadShortenedURL(string $url, PDO $db): string
    {
        if (!isset($db)) {
            return '';
        }
        $query = '
                SELECT ShortURLID, Destination, TimesUsed
                FROM ShortURLs
                WHERE Slug = ?';
        $stmt = $db->prepare($query);
        $params = [$url];
        $stmt->execute($params);
        $shortenedURLs = $stmt->fetchAll();
        if (count($shortenedURLs) > 0) {
            $item = $shortenedURLs[0];
            $timesUsed = (int)$item['TimesUsed'];
            $update = '
                    UPDATE ShortURLs SET DateLastUsed = ?, TimesUsed = ?
                    WHERE ShortURLID = ?';
            $params = [
                date('Y-m-d H:i:s'),
                $timesUsed + 1,
                $item['ShortURLID']
            ];
            $stmt = $db->prepare($update);
            $stmt->execute($params);
            return $item['Destination'];
        }
        return '';
    }
}
