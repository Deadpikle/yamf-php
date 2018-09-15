<?php

require_once 'yamf/models/View.php';

class HomeController {

    public function index($app, $request) {
        return new View('home/index', null, 'Home');
    }

}

?>