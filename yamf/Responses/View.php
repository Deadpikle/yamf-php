<?php

namespace Yamf\Responses;

use Yamf\AppConfig;

class View extends Response
{
    public $name;
    public $data;
    public $title; // (default: '')
    public $headerName; // e.g. 'templates/admin-header' (default: views/header.php)
    public $footerName; // e.g. 'admin/footer' (default: views/header.php)

    public $canUseDefaultHeader; // if false, will not use default header if $headerName is null. Defaults to true.
    public $canUseDefaultFooter; // if false, will not use default footer if $footerName is null. Defaults to true.

    public function __construct($name, $data = [], $title = '', $headerName = '', $footerName = '')
    {
        parent::__construct();
        $this->name = $name;
        $this->data = $data;
        $this->title = $title;
        $this->headerName = $headerName;
        $this->footerName = $footerName;
        $this->canUseDefaultHeader = true;
        $this->canUseDefaultFooter = true;
    }

    public function output(AppConfig $app)
    {
        parent::output($app);
        
        if ($this->data != null) {
            // add values to symbol table so that they can be used in the view
            extract($this->data); // http://php.net/manual/en/function.extract.php -- don't use on user input
        }
        if ($this->title && $this->title !== '') {
            $title = $this->title;
        }

        if ($this->headerName != null && $this->headerName !== '') {
            require 'views/' . $this->headerName . $app->viewExtension;
        } elseif ($this->canUseDefaultHeader && $app->defaultHeaderName !== null) {
            require 'views/' . $app->defaultHeaderName . $app->viewExtension;
        }
        
        require 'views/' . $this->name . $app->viewExtension;

        if ($this->footerName != null && $this->footerName !== '') {
            require 'views/' . $this->footerName . $app->viewExtension;
        } elseif ($this->canUseDefaultFooter && $app->defaultFooterName !== null) {
            require 'views/' . $app->defaultFooterName . $app->viewExtension;
        }
    }
}
