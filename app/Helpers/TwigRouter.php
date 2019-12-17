<?php

namespace App\Helpers;

use App\Models\Views\TwigNotFound;
use Yamf\AppConfig;
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
}
