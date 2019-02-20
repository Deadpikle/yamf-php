<?php

namespace Yamf;

use Yamf\Models\AppConfig;
use Yamf\Models\ErrorMessage;
use Yamf\Models\NotFound;
use Yamf\Models\Response;
use Yamf\Models\Request;

require_once 'yamf/functions.php';

class Router
{
    public function route(AppConfig $app, array $routes)
    {
        $request = $_SERVER['REQUEST_URI'];

        if (strEndsWith($request, '.php')) {
            // redirect to non-php file -- only works if file doesn't actually exist
            $request = substr($request, 0, -4);
            header("Location: $request");
            die();
        }

        $requestURL = str_replace($app->basePath, '', $request);

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
        } else {
            // see if there is a static URL or shortened URL
            // get the final path of the URL
            $path = parse_url($requestURL, PHP_URL_PATH);
            if ($path != null && $path !== '') {
                $pathParts = explode('/', $path);
                removeEmptyStringsFromArray($pathParts);
                // ok, the desired path is in the final section
                $pathCount = count($pathParts);
                if ($pathCount > 0) {
                    $fixedPath = implode('/', $pathParts);
                    $potentialFileName = 'views/static/' . $fixedPath . '.php';
                    if (file_exists($potentialFileName)) {
                        $title = ucfirst($pathParts[$pathCount - 1]);
                        if ($app->staticPageHeaderName != null) {
                            require_once 'views/' . $app->staticPageHeaderName . '.php';
                        }
                        require_once $potentialFileName;
                        if ($app->staticPageFooterName != null) {
                            require_once 'views/' . $app->staticPageFooterName . '.php';
                        }
                        die();
                    } elseif ($app->isShortURLEnabled && isset($app->db)) {
                        // see if route is a shortened URL since it isn't a static page
                        $potentialRoute = loadShortenedURL($fixedPath, $app->db);
                        if ($potentialRoute !== null && $potentialRoute != "") {
                            header("Location: $potentialRoute");
                            die();
                        }
                    }
                }
            }
            // couldn't determine route
            $notFound = new NotFound();
            $notFound->output($app);
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
        $isPost = isPostRequest();

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
        removeEmptyStringsFromArray($requestParts);
        $numberOfRequestParts = count($requestParts);
        // find the route
        foreach ($routes as $route => $path) {
            $routeParts = explode('/', $route);
            // remove empty request parts for ease of figuring out where we are
            removeEmptyStringsFromArray($routeParts);
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
                        if (strtolower($potentialPath[0]) == 'get' && !$isPost) {
                            $path = $potentialPath;
                            break;
                        } elseif (strtolower($potentialPath[0]) == 'post' && $isPost) {
                            $path = $potentialPath;
                            break;
                        }
                    }
                }
            } elseif (count($path) == 2 && $isPost) {
                continue; // didn't match up as the route is a GET route
            } elseif (count($path) === 3) {
                if (strtolower($path[0]) === 'get' && $isPost) {
                    continue;
                } elseif (strtolower($path[0]) === 'post' && !$isPost) {
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
}