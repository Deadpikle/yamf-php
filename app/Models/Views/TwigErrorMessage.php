<?php

namespace App\Models\Views;

use Yamf\AppConfig;

class TwigErrorMessage extends TwigView
{
    public $name;
    public $data;
    public $title; // (default: '')

    public function __construct($msg, $name = null, $title = null)
    {
        $msg = $msg ?? 'Sorry, the server encountered an error while performing your request!';
        $pageTitle = $title !== null ? $title : 'Error';
        $data = ['message' => $msg, 'title' => $pageTitle];
        parent::__construct($name !== null ? $name : 'errors/error', $data, $pageTitle);
        $this->statusCode = 400;
    }

    public function output(AppConfig $app)
    {
        parent::output($app);
    }
}
