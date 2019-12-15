<?php

use Yamf\Router;

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    echo 'Please install <a href="https://getcomposer.org">Composer</a> and run `composer install`! Thanks!';
    die();
}

// load configuration (sets up AppConfig $app)
require_once 'config.php';

// load routes
require_once 'routes.php';

// route the request
$router = new Router();
$router->route($app, $routes);
