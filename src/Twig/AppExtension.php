<?php

// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('className', [$this, 'getClassName']),
        ];
    }

    public function getClassName($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }
}