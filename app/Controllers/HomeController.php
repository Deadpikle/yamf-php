<?php

namespace App\Controllers;

use Yamf\AppConfig;
use Yamf\Request;

use Yamf\Responses\View;

class HomeController
{
    public function index(AppConfig $app, Request $request)
    {
        return new View('home/index', null, 'Home');
    }
}
