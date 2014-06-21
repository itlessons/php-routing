<?php

class UrlMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchHomepage()
    {
        $matcher = new \Routing\UrlMatcher();
        $matcher->register('GET', '/auth', 'app:auth:index');
        $matcher->register('GET', '/', 'app:home:index');

        $route = $matcher->match('GET', '/');

        $this->assertInstanceOf('\\Routing\\MatchedRoute', $route);
        $this->assertSame('app:home:index', $route->getController());
    }

    public function testMatchRequestMethods()
    {
        $matcher = new \Routing\UrlMatcher();
        $matcher->register('GET|POST', '/auth', 'app:auth:index');
        $matcher->register('GET', '/', 'app:home:index');

        $route = $matcher->match('POST', '/auth');
        $this->assertSame('app:auth:index', $route->getController());

        $route = $matcher->match('GET', '/auth');
        $this->assertSame('app:auth:index', $route->getController());

        $route = $matcher->match('PUT', '/auth');
        $this->assertNull($route);
    }

    public function testMatchPatterns()
    {
        $matcher = new \Routing\UrlMatcher();
        $matcher->register('GET', '/id(id:num)', 'app:user:index');
        $matcher->register('GET', '/search/(query:str)', 'app:search:index');
        $matcher->register('GET', '/tag/(tag:any)', 'app:tag:index');
        $matcher->register('GET', '/', 'app:home:index');
        $matcher->register('GET', '/blog/', 'app:blog:index');
        $matcher->register('GET', '/some/(page:num:?)', 'app:some:index');

        $route = $matcher->match('GET', '/id777');
        $this->assertSame('app:user:index', $route->getController());

        $route = $matcher->match('GET', '/id-777');
        $this->assertNull($route);

        $route = $matcher->match('GET', '/id72d');
        $this->assertNull($route);

        $route = $matcher->match('GET', '/search/12');
        $this->assertNull($route);

        $route = $matcher->match('GET', '/search/someword');
        $this->assertSame('app:search:index', $route->getController());

        $route = $matcher->match('GET', '/search/some-word');
        $this->assertSame('app:search:index', $route->getController());

        $route = $matcher->match('GET', '/search/someword89');
        $this->assertNull($route);

        $route = $matcher->match('GET', '/tag/so-mew_ord90');
        $this->assertSame('app:tag:index', $route->getController());

        $route = $matcher->match('GET', '/tag/so-mew_ord90/');
        $this->assertNull($route);
        $this->assertTrue($matcher->isNeedRedirect());
        $this->assertSame('/tag/so-mew_ord90', $matcher->getRedirectUrl());

        $route = $matcher->match('GET', '/id777/');
        $this->assertNull($route);
        $this->assertSame('/id777', $matcher->getRedirectUrl());

        $route = $matcher->match('GET', '/blog');
        $this->assertNull($route);
        $this->assertSame('/blog/', $matcher->getRedirectUrl());

        $route = $matcher->match('GET', '/blog/');
        $this->assertSame('app:blog:index', $route->getController());

        $route = $matcher->match('GET', '/some/1');
        $this->assertSame('app:some:index', $route != null ? $route->getController() : 'false');

        $route = $matcher->match('GET', '/some');
        $this->assertSame('app:some:index', $route != null ? $route->getController() : 'false');

        $route = $matcher->match('GET', '/some/');
        $this->assertNull($route);
        $this->assertSame('/some', $matcher->getRedirectUrl());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOptionalPlaceholder()
    {
        $matcher = new \Routing\UrlMatcher();
        $matcher->register('GET', '/(id:num:?)', 'app:some:index');
    }

    public function testCacheToFile()
    {
        $file = __DIR__ . '/cache/routes.cached.inc.php';

        if (is_file($file))
            unlink($file);

        $matcher = new \Routing\UrlMatcher();

        if (!$matcher->loadFromFile($file)) {
            $matcher->register('GET', '/id(id:num)', 'app:user:index');
            $matcher->register('GET', '/search/(query:str)', 'app:search:index');
            $matcher->register('GET', '/tag/(tag:any)', 'app:tag:index');
            $matcher->register('GET', '/', 'app:home:index');
            $matcher->register('GET', '/blog/', 'app:blog:index');
            $matcher->register('GET', '/some/(page:num:?)', 'app:some:index');
            $matcher->dumpToFile($file);
        }

        $route = $matcher->match('GET', '/search/some_str');

        $this->assertTrue(is_file($file));
        $this->assertSame('app:search:index', $route != null ? $route->getController() : 'false');

        $matcher = new \Routing\UrlMatcher();
        $matcher->loadFromFile($file);
        $this->assertSame('app:search:index', $route != null ? $route->getController() : 'false');
    }
} 