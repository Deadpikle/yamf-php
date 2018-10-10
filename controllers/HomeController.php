<?php

namespace Controllers;

use Yamf\Models\Request;
use Yamf\Models\View;

class HomeController
{
    public function index($app, Request $request)
    {
        return new View('home/index', null, 'Home');
    }
}
