<?php

namespace Yamf;

use Yamf\Util;

class AppConfig
{
    public $isLocalHost;
    public $basePath;
    
    /* Setup in config */
    public $defaultHeaderName;
    public $defaultFooterName;

    public $isShortURLEnabled;
    
    public $staticPageHeaderName; // change this value if you want a different header for static pages
    public $staticPageFooterName; // change this value if you want a different footer for static pages
    
    public $_404HeaderName; // change this value if you want a different 404 header to be used by the router
    public $_404Name; // change this value if you want a different 404 page to be used by the router
    public $_404FooterName; // change this value if you want a different 404 footer to be used by the router

    public $viewExtension; // change this value if you want to use a different file extension for your views
    public $staticViewExtension; // change this value if you want to use a different file extension for your static views
    
    /* Setup in config-private */
    public $db; // set up in config-private.php
    public $shouldShowErrorOnExceptionThrown;

    public function __construct($isLocalHost, $basePath)
    {
        $this->isLocalHost = $isLocalHost;
        $this->basePath = $basePath;
    }

    public function yurl(string $path) : string
    {
        if (!Util::strStartsWith($path, '/')) {
            return $this->basePath . '/' . $path;
        }
        return $this->basePath . $path;
    }
}
