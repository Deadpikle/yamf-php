<?php

namespace Yamf;

use Yamf\Util;

class AppConfig
{
    public $isLocalHost;
    public $basePath;
    
    /* Setup in config */
    public $defaultHeaderName; // defaults to 'header'
    public $defaultFooterName; // defaults to 'footer'

    public $isShortURLEnabled; // defaults to false
    
    public $staticPageHeaderName; // change this value if you want a different header for static pages
    public $staticPageFooterName; // change this value if you want a different footer for static pages
    
    public $notFoundHeaderName; // change this value if you want a different 404 header to be used by the router
    public $notFoundViewName; // change this value if you want a different 404 page to be used by the router
    public $notFoundFooterName; // change this value if you want a different 404 footer to be used by the router

    public $viewsFolderName; // this is the folder path (including trailing slash) from the root dir to the views directory
    public $staticViewsFolderName; // this is the folder path (including trailing slash) from the root dir to the static views directory
    public $viewExtension; // change this value if you want to use a different file extension for your views
    public $staticViewExtension; // change this value if you want to use a different file extension for your static views

    public $routerClass; // override this value to use your own custom Router child class
    
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
