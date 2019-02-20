<?php

namespace Controllers;

use Yamf\Models\AppConfig;
use Yamf\Models\Request;
use Yamf\Models\View;

class HomeController
{
    public function index(AppConfig $app, Request $request)
    {
        return new View('home/index', null, 'Home');
    }
}
