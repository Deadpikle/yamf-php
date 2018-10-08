<?php

namespace Yamf\Models;

class View extends Response
{
    public $name;
    public $data;
    public $title; // (default: '')
    public $headerName; // e.g. 'templates/admin-header' (default: views/header.php)
    public $footerName; // e.g. 'admin/footer' (default: views/header.php)

    public function __construct($name, $data = [], $title = '', $headerName = '', $footerName = '')
    {
        parent::__construct();
        $this->name = $name;
        $this->data = $data;
        $this->title = $title;
        $this->headerName = $headerName;
        $this->footerName = $footerName;
    }

    public function output($app)
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
            require 'views/' . $this->headerName . '.php';
        } elseif ($app->defaultHeaderName !== null) {
            require 'views/' . $app->defaultHeaderName . '.php';
        }
        
        require 'views/' . $this->name . '.php';

        if ($this->footerName != null && $this->footerName !== '') {
            require 'views/' . $this->footerName . '.php';
        } elseif ($app->defaultFooterName !== null) {
            require 'views/' . $app->defaultFooterName . '.php';
        }
    }
}
