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
    }
} 