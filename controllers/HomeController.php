<?php

namespace Controllers;

use Yamf\AppConfig;
use Yamf\Responses\Request;
use Yamf\Responses\View;

class HomeController
{
    public function index(AppConfig $app, Request $request)
    {
        return new View('home/index', null, 'Home');
    }
}
