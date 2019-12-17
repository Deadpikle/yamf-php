<?php

// from https://github.com/umpirsky/twig-php-function/

namespace App\ViewExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavbarLinkExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    function navbar_link($app, $name, $path, $basePath)
    {
        $currentRequest = str_replace($app->basePath, '', $_SERVER['REQUEST_URI']);
        if (\Yamf\Util::strEndsWith($currentRequest, '/')) {
            $currentRequest = substr($currentRequest, 0, -1);
        }
        $currentRequest = str_replace("/", "", $currentRequest);
        // figure out whether the current navbar item is active or not based on the URL
        $isCurrent = false;
        $pathNoSlashes = str_replace("/", "", $path);
        if ($path === "/" && $currentRequest === "") {
            $isCurrent = true;
        } elseif ($currentRequest !== "" && $pathNoSlashes != "" && strpos($currentRequest, $pathNoSlashes) !== false) {
            $isCurrent = true;
        }
        $liClass = $isCurrent ? "active" : "";
        return '<li class="' . $liClass . ' nav-item"><a class="nav-link" href="' . $basePath . $path . '">' . $name . '</a></li>';
    }

    public function getFunctions()
    {
        $twigFunctions = [
            new TwigFunction('navbar_link', [$this, 'navbar_link'])
        ];
        return $twigFunctions;
    }
}
