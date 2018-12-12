<?php
    
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    echo 'Please install <a href="https://getcomposer.org">Composer</a> and run `composer install`! Thanks!';
    die();
}

// Setup $app variable for application wide variables that you might need in
// controllers or in views (e.g. database connection)
$app = new stdClass;

$whitelist = [
    '127.0.0.1',
    '::1'
];

$app->isLocalHost = in_array($_SERVER['REMOTE_ADDR'], $whitelist);
$app->basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));

// load user configuration files
require_once 'config.php';
if (file_exists('config-private.php')) {
    require_once 'config-private.php';
}

require_once 'yamf/router.php';
