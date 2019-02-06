<?php

namespace Yamf\Models;

/**
 * Simple wrapper around View to send back a 404 status code and the 404.php view
 */
class NotFound extends View
{
    public function __construct($name = '404', $data = [], $title = '', $headerName = '', $footerName = '')
    {
        parent::__construct($name, $data, $title, $headerName, $footerName);
        $this->canUseDefaultHeader = false;
        $this->canUseDefaultFooter = false;
        $this->statusCode = 404;
    }

    public function output($app)
    {
        $this->name = $app->_404Name;
        $this->headerName = $app->_404HeaderName;
        $this->footerName = $app->_404FooterName;
        parent::output($app);
    }
}
