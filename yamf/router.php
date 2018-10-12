<?php

use Yamf\Models\Response;
use Yamf\Models\ErrorMessage;

require_once 'routes.php';
require_once 'yamf/functions.php';

$request = $_SERVER['REQUEST_URI'];

if (strEndsWith($request, '.php')) {
    // redirect to non-php file -- only works if file doesn't actually exist
    $request = substr($request, 0, -4);
    header("Location: $request");
    die();
}

$requestURL = str_replace($app->basePath, '', $request);

$request = findRoute($routes, $requestURL);
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
    if ($app->_404HeaderName !== null) {
        require_once 'views/' . $app->_404HeaderName . '.php';
    }
    require_once 'views/' . $app->_404Name . '.php';
    if ($app->_404FooterName !== null) {
        require_once 'views/' . $app->_404FooterName . '.php';
    }
}
