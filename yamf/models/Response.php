<?php

namespace Yamf\models;

class Response {
    public $statusCode;

    public function __construct($statusCode = 200) {
        $this->statusCode = $statusCode;
    }

    public function output($app) {
        http_response_code($this->statusCode);
    }
}
