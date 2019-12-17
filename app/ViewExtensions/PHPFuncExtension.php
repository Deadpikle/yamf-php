<?php

// from https://github.com/umpirsky/twig-php-function/

namespace App\ViewExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PHPFuncExtension extends AbstractExtension
{
    private $functions = array(
        'uniqid',
        'floor',
        'ceil',
        'addslashes',
        'chr',
        'chunk_​split',
        'convert_​uudecode',
        'crc32',
        'crypt',
        'hex2bin',
        'md5',
        'sha1',
        'strpos',
        'strrpos',
        'ucwords',
        'wordwrap',
        'gettype',
        'filemtime'
    );

    public function __construct(array $functions = array())
    {
        if ($functions) {
            $this->allowFunctions($functions);
        }
    }

    public function getFunctions()
    {
        $twigFunctions = array();
        foreach ($this->functions as $function) {
            $twigFunctions[] = new TwigFunction($function, $function);
        }
        return $twigFunctions;
    }

    public function allowFunction($function)
    {
        $this->functions[] = $function;
    }

    public function allowFunctions(array $functions)
    {
        $this->functions = $functions;
    }

    public function getName()
    {
        return 'php_function';
    }
}
