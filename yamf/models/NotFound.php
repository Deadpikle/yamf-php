<?php

require_once 'View.php';

/**
 * Simple wrapper around View to send back a 404 status code and the 404.php view
 */
class NotFound extends View {
    
    public function __construct($name = '404', $data = [], $title = '', $headerName = '', $footerName = '') {
        parent::__construct($name, $data, $title, $headerName, $footerName);
        $this->statusCode = 404;
    }

    public function output($app) {
        parent::output($app);
    }
}

?>