<?php

namespace Yamf\Responses;

use Yamf\AppConfig;

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

    public function output(AppConfig $app)
    {
        $this->name = $app->notFoundHeaderName;
        $this->headerName = $app->notFoundViewName;
        $this->footerName = $app->notFoundFooterName;
        parent::output($app);
    }
}
