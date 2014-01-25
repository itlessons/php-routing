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


URL Generating Only
-------------------

You can use url generator standalone:

    use Routing\UrlGenerator;

    $generator = new UrlGenerator('http://domain.tld');
    $generator->add('home', '/');
    $generator->add('hello', '/hello');
    $generator->add('profile', '/user(:id)');

    $url = $generator->generate('profile', array('id' => 888), true);

Resources
---------

You can run the unit tests with the following command:

    $ cd path/to/php-routing/
    $ composer.phar install
    $ phpunit