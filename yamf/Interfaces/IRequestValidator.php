<?php

namespace Yamf\Interfaces;

use Yamf\AppConfig;
use Yamf\Request;
use Yamf\Responses\Response;

/**
 * Validate a request before the normal controller method is called.
 */
interface IRequestValidator
{
    /**
     * Validate a request before the normal controller method is called.
     * 
     * Return null if the request is valid. Otherwise, return a response
     * that will be output to the user rather than the normal controller method.
     */
    public function validateRequest(AppConfig $app, Request $request) : ?Response;
}