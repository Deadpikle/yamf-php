<?php
    
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
    }

    // Setup $app variable for application wide variables that you might need in 
    // controllers or in views (e.g. database connection)
    $app = new stdClass;

    $whitelist = array(
        '127.0.0.1',
        '::1'
    );

    $app->isLocalHost = in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    $app->basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));
    
    if (file_exists('config.php')) {
        require_once 'config.php';
    }
    else {
        echo 'Please finish site setup by copying config.sample.php to config.php. Thanks!';
        die();
    }

    require_once 'yamf/router.php';
?>