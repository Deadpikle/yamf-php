<?php

namespace Yamf\Responses;

use Yamf\AppConfig;

class Response
{
    public $statusCode;

    public function __construct($statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    public function output(AppConfig $app)
    {
        http_response_code($this->statusCode);
    }
}
