<?php

namespace Controllers;

use Yamf\models\Request;
use Yamf\models\View;

class HomeController {

    public function index($app, Request $request) {
        return new View('home/index', null, 'Home');
    }
}
