<?php

namespace App\Models\Views;

use App\ViewExtensions\NavbarLinkExtension;
use Exception;
use Yamf\AppConfig;
use Yamf\Responses\Response;

use App\ViewExtensions\PHPFuncExtension;

class TwigView extends Response
{
    public $name;
    public $data;
    public $title; // (default: '')

    public function __construct($name, $data = [], $title = '')
    {
        parent::__construct();
        $this->name = $name;
        $this->data = $data;
        $this->title = $title;
    }

    public function output(AppConfig $app)
    {
        parent::output($app);

        $loader = new \Twig\Loader\FilesystemLoader($app->viewsFolderName);
        $twig = new \Twig\Environment($loader, [
            'cache' => 'views/_cache',
            'auto_reload' => true
        ]);
        $twig->addExtension(new PHPFuncExtension());
        $twig->addExtension(new NavbarLinkExtension());

        if ($this->data !== null) {
            $this->data['app'] = $app;
            $this->data['title'] = $this->title;
        }

        $filename = $this->name . '.twig';
        try {
            echo $twig->render($filename, $this->data ?? ['app' => $app]);
        } catch (Exception $e) {
            echo $e;
        }
    }
}
