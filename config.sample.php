<?php
    // Set up any config parameters you need in this file, such as reCAPTCHA, Google Maps API, 
    // database connections, etc. Add it to the $app class if you want it to automatically 
    // be available to your controllers and views.

    // Over time, YAMF may have new config parameters. Any of those parameters will show up
    // BELOW all other parameters, so, we suggest that you add any of your own, custom
    // ones above all YAMF built-in options. Each section will be preceded by the YAMF
    // version number that the setting was first introduced in. Make sure to read
    // release notes when updating YAMF versions so that you're aware of any changes that
    // have been made!

    // If you want some of these to be saved in your repo instead of ignored by git,
    // just add them to an additional config file of your own name (such as my-app-config.php)
    // and require_once 'my-app-config.php' to include those settings. Remember not to
    // put any sensitive credentials in a repo where malicious users might be able
    // to grab them!

    // // // // // // // Session Settings // // // // // // //

    /*
    // What follows is some very basic session logic that you can use to start your PHP session logic.
    // It's not guaranteed to be very good, but should get you started. (Open to pull request improvements!)
    $sessionTime = 3600 * 24;
    ini_set('session.gc_maxlifetime', $sessionTime);
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
    //$app->recaptchaPrivateKey = "[key here]";
    //$app->recaptchaPublicKey = "[key here]";
    //$app->mapsAPIKey = "[key here]";
    //$app->isAdmin = isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'WebAdmin';

    // // // // // YAMF Settings v1.0 // // // // //

    /* 
    // Uncomment these lines if you want there to be a database connection available through $app->db
    try {
        $host = '127.0.0.1';
        $db   = 'db-name';
        $user = 'username';
        $pass = 'password';
        $charset = 'utf8';
    
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $app->db = new PDO($dsn, $user, $pass, $opt);
    }
    catch (Exception $e) {
        echo "DB connection details are invalid: " . $e->getMessage();
    }*/

    /* Change isShortURLEnabled to true if you want to enable routing
       logic for shortened URLs. You'll want a table with the following
       schema available in the db with the above $app->db connection:
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

    $app->shouldShowErrorOnExceptionThrown = false; // whether or not to show the error page on an exception being thrown from a controller
    