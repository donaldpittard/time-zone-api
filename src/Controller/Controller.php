<?php
namespace App\Controller;

use Slim\Container;

class Controller
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}