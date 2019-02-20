<?php

// Set up any session/config parameters you need in for your website that are safe
// to add to your repo, such as session logic, etc. 
// Add the info to the $app class if you want it to automatically 
// be available to your controllers and views. This file contains 
// some YAMF configuration parameters, too.

// DO NOT save any private info to this file, including database credentials, etc.
// Sharing such credentials in any form is dangerous for a multitude of reasons.

// Over time, YAMF may have new config parameters. Any of those parameters will show up
// BELOW all other parameters, so, we suggest that you add any of your own, custom
// ones above all YAMF built-in options. Each section will be preceded by the YAMF
// version number that the setting was first introduced in. Make sure to read
// release notes when updating YAMF versions so that you're aware of any changes that
// have been made!


// Initialize AppConfig object

// If you want to change the class for $app, your class *must* derive from Yamf\Models\AppConfig
$appConfigClass = 'Yamf\AppConfig';

$whitelist = [
    '127.0.0.1',
    '::1'
];

$app = new $appConfigClass(
    in_array($_SERVER['REMOTE_ADDR'], $whitelist), 
    str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__)
));

// First, load private config so that we have a db connection if we need one for any initialization.
if (file_exists('config-private.php')) {
    require_once 'config-private.php';
}

// // // // // // // Session Settings // // // // // // //

/*
// What follows is some very basic session logic that you can use to start your PHP session logic.
// It's not guaranteed to be very good, but should get you started. (Open to pull request improvements!)
$sessionTime = 3600 * 24;
ini_set('session.gc_maxlifetime', $sessionTime);
session_name('yamf');
session_start();

// https://stackoverflow.com/a/1270960/3938401
// 3600 * 24 => 24 hours
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionTime)) {
    // last request was more than 24 hrs ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp*/

// // // // // // // User Settings // // // // // // //

// Examples of settings or other config parameters you might want:
// $app->isAdmin = isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'WebAdmin';

// // // // // YAMF Settings v1.0 // // // // //

/* Change isShortURLEnabled to true if you want to enable routing
    logic for shortened URLs. You'll want a table with the following
    schema available in the db with the a PDO $app->db connection:
    CREATE TABLE `ShortURLs` (
    `ShortURLID` int(11) NOT NULL,
    `Slug` varchar(1000) NOT NULL,
    `Destination` varchar(7500) NOT NULL,
    `DateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `DateLastUsed` datetime DEFAULT NULL,
    `TimesUsed` int(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8; */

$app->isShortURLEnabled = false;    

// Headers and Footers //

// If you want headers and footers to not be used at all for the following items,
//  set the value to null.

$app->defaultHeaderName = 'header'; // change this value if you want a different default header
$app->defaultFooterName = 'footer'; // change this value if you want a different default header

$app->staticPageHeaderName = 'header'; // change this value if you want a different header for static pages
$app->staticPageFooterName = 'footer'; // change this value if you want a different footer for static pages

$app->_404HeaderName = 'header'; // change this value if you want a different 404 header to be used by the router
$app->_404Name = '404'; // change this value if you want a different 404 page to be used by the router
$app->_404FooterName = 'footer'; // change this value if you want a different 404 footer to be used by the router

$app->viewsFolderName = 'views/'; // this is the folder path (including trailing slash) from the root dir to the views directory
$app->staticViewsFolderName = 'views/static/'; // this is the folder path (including trailing slash) from the root dir to the static views directory

$app->viewExtension = '.php'; // change this value if you want to use a different file extension for your views
$app->staticViewExtension = '.php'; // change this value if you want to use a different file extension for your static views
