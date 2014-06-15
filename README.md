PHP Routing Library
===================

Routing associates a request with the code that will convert it to a response.

The example below demonstrates how you can set up a fully working routing
system:

    use Routing\Router;

    $host = 'http://domain.tld';

    $router = new Router($host);

    $router->add('home', '/', 'controller:action');
    $router->add('hello', '/hello', 'static:welcome', 'GET');
    $router->add('profile', '/user(id:num)', 'profile:index', 'GET|POST');

    $route = $router->match('GET', '/user777');
    // $route->getController() => 'static:welcome'
    // $route->getParameters() => [id:777]

    $url = $router->generate('profile', array('id' => 777));
    // $url => /user777

    $url = $router->generate('profile', array('id' => 777), true);
    // $url => http://domain.tld/user777


URL Matching Only
-----------------

You can use url matcher standalone:

    use Routing\UrlMatcher;

    $matcher = new UrlMatcher();
    $matcher->register('GET', '/', 'controller:action');
    $matcher->register('GET', '/hello', 'static:welcome');
    $matcher->register('GET|POST', '/user(id:num)', 'profile:index');

    $route = $router->match('GET', '/hello');

    // redirect if need (e.g /blog -> /blog/ if /blog/ exists)
    if($matcher->isNeedRedirect()){
        redirect($matcher->getRedirectUrl(), 302);
    }


URL Generating Only
-------------------

You can use url generator standalone:

    use Routing\UrlGenerator;

    $generator = new UrlGenerator('http://domain.tld');
    $generator->add('home', '/');
    $generator->add('hello', '/hello');
    $generator->add('profile', '/user(:id)');

    $url = $generator->generate('profile', array('id' => 888), true);


REQUEST CLASS HELPER
--------------------

You can use simple request class to find pathInfo:

    use Routing\Router;
    use Routing\Request;

    $request = new Request();

    $router = new Router($request->getHTTPHost());

    $router->add('home', '/', 'controller:action');
    $router->add('hello', '/hello', 'static:welcome', 'GET');
    $router->add('profile', '/user(id:num)', 'profile:index', 'GET|POST');

    $route = $router->match($request->getMethod(), $request->getPathInfo());


Resources
---------

You can run the unit tests with the following command:

    $ cd path/to/php-routing/
    $ composer.phar install
    $ phpunit

Links
-----
* [Система роутинга на сайте с помощью PHP](http://www.itlessons.info/php/routing-library/)
* [Base example source](http://demos.itlessons.info/res/024-php-routing.zip)
