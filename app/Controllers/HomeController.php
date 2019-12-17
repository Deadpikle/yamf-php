<?php

namespace App\Controllers;

use Yamf\AppConfig;
use Yamf\Request;

use Yamf\Responses\View;

use App\Models\Views\TwigView;

class HomeController
{
    public function index(AppConfig $app, Request $request)
    {
        return new TwigView('home/index', ['name' => 'cow'], 'Home');
    }

    public function about(AppConfig $app, Request $request)
    {
        return new TwigView('home/about', [], 'About');
    }
}
