<?php


class RequestTest extends \PHPUnit_Framework_TestCase {

    public function testCreate(){

        $request = self::create('http://test.com/foo?bar=baz');
        $this->assertInstanceOf('\Routing\Request', $request);
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertEquals('http://test.com', $request->getHTTPHost());
        $this->assertFalse($request->isHTTPS());
        $this->assertFalse($request->isPost());

        $request = self::create('https://test.com/foo/var/10?bar=baz', 'POST');
        $this->assertEquals('/foo/var/10', $request->getPathInfo());
        $this->assertTrue($request->isHTTPS());
        $this->assertTrue($request->isPost());
    }

    private static function create($uri, $method = 'GET', $server = array())
    {
        $server = array_replace(array(
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'PHP-routing request',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
        ), $server);

        $server['REQUEST_URI'] = $uri;
        $server['PATH_INFO'] = '';
        $server['REQUEST_METHOD'] = strtoupper($method);

        $components = parse_url($uri);
        if (isset($components['host'])) {
            $server['SERVER_NAME'] = $components['host'];
            $server['HTTP_HOST'] = $components['host'];
        }

        if (isset($components['scheme'])) {
            if ('https' === $components['scheme']) {
                $server['HTTPS'] = 'on';
                $server['SERVER_PORT'] = 443;
            } else {
                unset($server['HTTPS']);
                $server['SERVER_PORT'] = 80;
            }
        }

        if (isset($components['port'])) {
            $server['SERVER_PORT'] = $components['port'];
            $server['HTTP_HOST'] = $server['HTTP_HOST'] . ':' . $components['port'];
        }

        if (isset($components['user'])) {
            $server['PHP_AUTH_USER'] = $components['user'];
        }

        if (isset($components['pass'])) {
            $server['PHP_AUTH_PW'] = $components['pass'];
        }

        return new \Routing\Request($server);
    }
} 