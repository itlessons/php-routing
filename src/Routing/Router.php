<?php

namespace Routing;

class Router
{
    private $routes = array();
    private $host;
    private $mather;
    private $generator;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function add($name, $pattern, $controller, $method = 'GET')
    {
        $this->routes[$name] = array(
            'pattern' => $pattern,
            'controller' => $controller,
            'method' => $method,
        );
    }

    /**
     * @param $method
     * @param $uri
     * @return MatchedRoute
     */
    public function match($method, $uri)
    {
        return $this->getMatcher()->match($method, $uri);
    }

    public function generate($name, array $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }

    /**
     * @return UrlMatcher
     */
    private function getMatcher()
    {
        if (null == $this->mather) {
            $this->mather = new UrlMatcher();
            foreach ($this->routes as $route) {
                $this->mather->register($route['method'], $route['pattern'], $route['controller']);
            }
        }

        return $this->mather;
    }

    /**
     * @return UrlGenerator
     */
    private function getGenerator()
    {
        if (null == $this->generator) {
            $this->generator = new UrlGenerator($this->host);
            foreach ($this->routes as $name => $route) {
                $pattern = preg_replace('#\((\w+):(\w+)\)#', '(:$1)', $route['pattern']);
                $this->generator->add($name, $pattern);
            }
        }

        return $this->generator;
    }
}