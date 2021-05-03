<?php

namespace App\Helpers;

use App\Models\Views\TwigErrorMessage;
use App\Models\Views\TwigNotFound;
use Yamf\AppConfig;
use Yamf\Responses\Response;
use Yamf\Router;

class TwigRouter extends Router
{
    /**
     * Shows the 404 not found page
     * 
     * @param AppConfig $app
     */
    public function showNotFound(AppConfig $app) : void
    {
        $notFound = new TwigNotFound();
        $notFound->output($app);
    }

    /**
     * Shows an error on routing exception
     * 
     * @param AppConfig $app
     * @param Exception $e
     */
    public function showErrorOnException(AppConfig $app, \Throwable $e) : void
    {
        if (isset($app->shouldShowErrorOnExceptionThrown)) {
            if ($app->shouldShowErrorOnExceptionThrown) {
                $response = new TwigErrorMessage($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                $response->statusCode = 500;
            }
            else {
                $response = new Response(500);
            }
            $response->output($app);
        }
    }
}
