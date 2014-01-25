<?php

namespace Routing;

class UrlMatcher
{
    private $methods = array(
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'HEAD'
    );

    private $routes = array(
        'GET' => array(),
        'POST' => array(),
        'PUT' => array(),
        'DELETE' => array(),
        'PATCH' => array(),
        'HEAD' => array(),
    );

    private $patterns = array(
        'num' => '[0-9]+',
        'str' => '[a-zA-Z\.\-_%]+',
        'any' => '[a-zA-Z0-9\.\-_%]+',
    );

    public function addPattern($name, $pattern)
    {
        $this->patterns[$name] = $pattern;
    }

    public function register($method, $route, $controller)
    {
        $methods = strtoupper($method);

        if (false !== strpos($methods, '|')) {
            $methods = explode('|', $methods);
        }

        if ($methods == '*') {
            $methods = $this->methods;
        }

        $methods = (array)$methods;

        $converted = $this->convertRoute($route);

        foreach ($methods as $m) {
            $this->routes[$m][$converted] = $controller;
        }
    }

    private function convertRoute($route)
    {
        if (false === strpos($route, '(')) {
            return $route;
        }

        return preg_replace_callback('#\((\w+):(\w+)\)#', array($this, 'replaceRoute'), $route);
    }

    private function replaceRoute($match)
    {
        $name = $match[1];
        $pattern = $match[2];

        return '(?<' . $name . '>' . strtr($pattern, $this->patterns) . ')';
    }

    /**
     * @param $method
     * @param $uri
     * @return MatchedRoute
     */
    public function match($method, $uri)
    {
        $method = strtoupper($method);
        $routes = $this->routes($method);

        if (array_key_exists($uri, $routes)) {
            return new MatchedRoute($routes[$uri]);
        }

        return $this->doMatch($method, $uri);
    }

    private function routes($method)
    {
        return isset($this->routes[$method]) ? $this->routes[$method] : array();
    }

    private function doMatch($method, $uri)
    {
        foreach ($this->routes($method) as $route => $controller) {
            if (false !== strpos($route, '(')) {
                $pattern = '#^' . $route . '$#s';

                if (preg_match($pattern, $uri, $parameters)) {
                    return new MatchedRoute($controller, $this->processParameters($parameters));
                }
            }
        }
    }

    private function processParameters($parameters)
    {
        foreach ($parameters as $k => $v) {
            if (is_int($k)) {
                unset($parameters[$k]);
            }
        }

        return $parameters;
    }
}