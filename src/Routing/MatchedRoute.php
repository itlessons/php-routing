<?php

namespace Routing;

class MatchedRoute
{
    private $controller;
    private $parameters;

    public function __construct($controller, array $parameters = array())
    {
        $this->controller = $controller;
        $this->parameters = $parameters;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}