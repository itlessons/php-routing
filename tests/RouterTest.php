<?php

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $host = 'http://domain.tld';

    public function testBase()
    {
        $router = new \Routing\Router($this->host);
        $router->add('blog', '/blog/(page:num:?)', 'app:blog:index');

        $this->assertSame('/blog/1', $router->generate('blog', array('page' => 1)));
        $this->assertSame('/blog', $router->generate('blog'));

        $route = $router->match('GET', '/blog');
        $this->assertSame('app:blog:index', $route != null ? $route->getController() : 'false');
    }

    public function testCacheToFile()
    {
        $router = new \Routing\Router($this->host);

        $fileG = __DIR__ . '/cache/generator.cached.inc.php';
        $fileM = __DIR__ . '/cache/routes.cached.inc.php';

        if (is_file($fileG))
            unlink($fileG);

        if (is_file($fileM))
            unlink($fileM);

        $router->useCache($fileM, $fileG);
        $router->add('blog', '/blog/(page:num:?)', 'app:blog:index');
        $router->add('user', '/id(id:num)', 'app:user:index');

        $route = $router->match('GET', '/id888');
        $this->assertSame('app:user:index', $route != null ? $route->getController() : 'false');
        $this->assertSame('/id777', $router->generate('user', array('id' => 777)));
        $this->assertTrue(is_file($fileG));
        $this->assertTrue(is_file($fileM));

        $router = new \Routing\Router($this->host);
        $router->useCache($fileM, $fileG);
        $route = $router->match('GET', '/id888');
        $this->assertSame('app:user:index', $route != null ? $route->getController() : 'false');
        $this->assertSame('/id777', $router->generate('user', array('id' => 777)));
        $this->assertTrue(is_file($fileG));
        $this->assertTrue(is_file($fileM));
    }
} 