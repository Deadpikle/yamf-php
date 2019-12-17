<?php

namespace App\Models\Views;

use Yamf\AppConfig;

class TwigNotFound extends TwigView
{
    public $name;
    public $data;
    public $title; // (default: '')

    public function __construct($name = null, $title = null)
    {
        $msg = $msg ?? 'Sorry, but the page you are looking for does not exist!';
        $pageTitle = $title !== null ? $title : 'Not Found';;
        $data = ['message' => $msg, 'title' => $title];
        parent::__construct($name !== null ? $name : 'errors/404', $data, $pageTitle);
        $this->statusCode = 404;
    }

    public function output(AppConfig $app)
    {
        parent::output($app);
    }
}
