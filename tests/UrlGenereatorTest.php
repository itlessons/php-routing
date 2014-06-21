<?php

class UrlGenereatorTest extends \PHPUnit_Framework_TestCase
{
    private $host = 'http://domain.tld';

    public function testGenerateHomepage()
    {
        $generator = new \Routing\UrlGenerator($this->host);
        $generator->add('home', '/');

        $this->assertSame('/', $generator->generate('home'));
        $this->assertSame($this->host . '/', $generator->generate('home', array(), true));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionWrongRouteName()
    {
        $generator = new \Routing\UrlGenerator($this->host);
        $generator->add('home', '/');

        $generator->generate('wrong_name');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionMissingParameters()
    {
        $generator = new \Routing\UrlGenerator($this->host);
        $generator->add('user', '/id(:id)');

        $generator->generate('user', array('page' => 1));
    }

    public function testExtraParameters()
    {
        $generator = new \Routing\UrlGenerator($this->host);
        $generator->add('user', '/id(:id)');

        $url = $generator->generate('user', array('id' => 777, 'page' => 1));

        $this->assertSame('/id777?page=1', $url);
    }

    public function testGenerate()
    {
        $generator = new \Routing\UrlGenerator($this->host);
        $generator->add('user', '/id(:id).html');
        $generator->add('confirm', '/confirm/(:user_id)-(:code)');
        $generator->add('blog', '/blog/(:page:?)');

        $this->assertSame('/id888.html', $generator->generate('user', array('id' => 888)));

        $this->assertSame(
            '/confirm/6-some-code88', $generator->generate(
            'confirm',
            array(
                'user_id' => 6,
                'code' => 'some-code88'
            )
        ));

        $this->assertSame('/blog/1', $generator->generate('blog', array('page' => 1)));
        $this->assertSame('/blog', $generator->generate('blog'));
    }

    public function testCacheToFile()
    {
        $file = __DIR__ . '/cache/generator.cached.inc.php';
        $host = 'http://domain.tld';

        if (is_file($file))
            unlink($file);

        $gen = new \Routing\UrlGenerator($host);

        if (!$gen->loadFromFile($file)) {
            $gen->add('user', '/id(:id).html');
            $gen->add('confirm', '/confirm/(:user_id)-(:code)');
            $gen->add('blog', '/blog/(:page:?)');
            $gen->dumpToFile($file);
        }

        $this->assertSame('/id888.html', $gen->generate('user', array('id' => 888)));

        $gen = new \Routing\UrlGenerator($host);
        $gen->loadFromFile($file);
        $this->assertSame('/id777.html', $gen->generate('user', array('id' => 777)));
    }
} 