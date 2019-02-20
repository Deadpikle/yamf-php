<?php

if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} else {
    echo 'Please install <a href="https://getcomposer.org">Composer</a> and run `composer install`! Thanks!';
    die();
}

// load configuration
require_once 'config.php';

// route the request
require_once 'yamf/router.php';
