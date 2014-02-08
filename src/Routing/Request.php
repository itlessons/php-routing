<?php

namespace Routing;

class Request
{
    private $data;
    private $pathInfo;

    public function __construct($data = null)
    {
        $this->data = $data != null ? $data : $_SERVER;
    }

    public function isPost()
    {
        return $this->get('REQUEST_METHOD') == 'POST';
    }

    public function isMethod($method)
    {
        return $this->getMethod() == strtoupper($method);
    }

    public function isHTTPS()
    {
        return $this->has('HTTPS') && $this->get('HTTPS') != 'off';
    }

    public function getMethod()
    {
        $method = $this->get('REQUEST_METHOD');

        if ($this->isPost()) {
            if ($this->has('X-HTTP-METHOD-OVERRIDE')) {
                $method = strtoupper($this->get('X-HTTP-METHOD-OVERRIDE'));
            }
        }

        return $method;
    }

    public function getHTTPHost()
    {
        $host = $this->isHTTPS() ? 'https://' : 'http://';
        $host .= $this->getHost();
        return $host;
    }

    public function getHost()
    {
        $host = $this->get('HTTP_HOST');

        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        if ($host && !preg_match('/^\[?(?:[a-zA-Z0-9-:\]_]+\.?)+$/', $host)) {
            throw new \UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }

        return $host;
    }

    public function getPathInfo($baseUrl = null)
    {
        if (null != $this->pathInfo)
            return $this->pathInfo;

        $pathInfo = $this->get('REQUEST_URI');

        if (!$pathInfo) {
            $pathInfo = '/';
        }

        $schemeAndHttpHost = $this->isHTTPS() ? 'https://' : 'http://';
        $schemeAndHttpHost .= $this->get('HTTP_HOST');

        if (strpos($pathInfo, $schemeAndHttpHost) === 0) {
            $pathInfo = substr($pathInfo, strlen($schemeAndHttpHost));
        }

        if ($pos = strpos($pathInfo, '?')) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        if (null != $baseUrl) {
            $pathInfo = substr($pathInfo, strlen($pathInfo));
        }

        if (!$pathInfo) {
            $pathInfo = '/';
        }

        return $this->pathInfo = $pathInfo;
    }

    public function get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function has($name)
    {
        return $this->get($name) != null;
    }
}