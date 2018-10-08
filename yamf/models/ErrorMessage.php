<?php

namespace Yamf\models;

/**
 * Simple wrapper around View to send back a 400 status code and the error.php view. 
 * Of course, if you want to send back a different status code, it's as easy as
 * setting $notFound->statusCode = XYZ.
 * Be forewarned that the first parameter is the error message, NOT the name of the view!
 * This is just for ease of use since most people will not be changing the view name.
 */
class ErrorMessage extends View {
    public function __construct($msg = '', $name = 'error', $title = '', $headerName = '', $footerName = '') {
        $msg = $msg ?? 'Sorry, the server encountered an error while performing your request!';

        parent::__construct($name, ['error' => $msg], $title, $headerName, $footerName);
        $this->statusCode = 400;
    }

    public function output($app) {
        parent::output($app);
    }
}
