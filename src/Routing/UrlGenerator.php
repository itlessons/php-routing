<?php

namespace Routing;

class UrlGenerator
{
    private $map = array();
    private $mapData = array();
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function add($name, $pattern)
    {
        $this->map[$name] = $pattern;
    }

    public function has($name)
    {
        return isset($this->map[$name]);
    }

    /**
     * Generate url by route.
     *
     * @param  string $name
     * @param  array $parameters
     * @param  boolean $absolute
     * @throws \InvalidArgumentException
     * @return string
     */
    public function generate($name, array $parameters = array(), $absolute = false)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Rule for identifier "%s" not found', $name));
        }

        $this->compilePattern($name);

        if (($diff = array_diff_key($this->mapData[$name], $parameters))) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" route has some missing parameters ("%s").',
                $name,
                implode('", "', array_keys($diff))
            ));
        }

        $pattern = $this->map[$name];
        $rParameters = array();
        $extra = array();

        foreach ($parameters as $k => $v) {
            if (isset($this->mapData[$name][$k])) {
                $rName = '(:' . $k . ')';
                $rParameters[$rName] = $v;
            } else {
                $extra[$k] = $v;
            }
        }

        $url = strtr($pattern, $rParameters);

        if (count($extra)) {
            $url .= '?' . http_build_query($extra);
        }

        if ($absolute) {
            $url = $this->host . $url;
        }

        return $url;
    }

    private function compilePattern($name)
    {
        if (isset($this->mapData[$name])) {
            return;
        }

        $pattern = $this->map[$name];
        $matches = array();

        if (preg_match_all('#\(:(\w+)\)#', $pattern, $matches)) {
            $this->mapData[$name] = array_flip($matches[1]);
        } else {
            $this->mapData[$name] = array();
        }
    }
} 