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

        $this->assertSame('/id888.html', $generator->generate('user', array('id' => 888)));

        $this->assertSame(
            '/confirm/6-some-code88', $generator->generate(
            'confirm',
            array(
                'user_id' => 6,
                'code' => 'some-code88'
            )
        ));
    }
} 