<?php

namespace Routing;

class Router
{
    private $routes = array();
    private $host;
    private $matcher;
    private $generator;
    private $matcherCacheFile;
    private $generatorCacheFile;

    public function __construct($host = null)
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
    public function getMatcher()
    {
        if (null == $this->matcher) {
            $this->matcher = new UrlMatcher();

            if ($this->matcherCacheFile && $this->matcher->loadFromFile($this->matcherCacheFile)) {
                return $this->matcher;
            }

            foreach ($this->routes as $route) {
                $this->matcher->register($route['method'], $route['pattern'], $route['controller']);
            }

            if ($this->matcherCacheFile) {
                $this->matcher->dumpToFile($this->matcherCacheFile);
            }

        }

        return $this->matcher;
    }

    /**
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if (null == $this->generator) {
            $this->generator = new UrlGenerator($this->host);

            if ($this->generatorCacheFile && $this->generator->loadFromFile($this->generatorCacheFile)) {
                return $this->generator;
            }

            foreach ($this->routes as $name => $route) {
                $pattern = preg_replace('#\((\w+):(\w+):\?\)#', '(:$1:?)', $route['pattern']);
                $pattern = preg_replace('#\((\w+):(\w+)\)#', '(:$1)', $pattern);
                $this->generator->add($name, $pattern);
            }

            if ($this->generatorCacheFile) {
                $this->generator->dumpToFile($this->generatorCacheFile);
            }
        }

        return $this->generator;
    }

    public function useCache($matcherCacheFile, $generatorCacheFile)
    {
        $this->matcherCacheFile = $matcherCacheFile;
        $this->generatorCacheFile = $generatorCacheFile;
    }

    public function setHost($host)
    {
        $this->host = $host;

        if ($this->generator) {
            $this->generator->setHost($host);
        }
    }
}