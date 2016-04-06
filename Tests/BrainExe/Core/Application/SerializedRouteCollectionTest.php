<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\SerializedRouteCollection;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Routing\Route;

class SerializedRouteCollectionTest extends TestCase
{

    /**
     * @var SerializedRouteCollection
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new SerializedRouteCollection();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage invalid route: bar
     */
    public function testGetInvalid()
    {
        $this->subject->get('bar');
    }

    public function testCount()
    {
        $actual = $this->subject->count();

        $this->assertGreaterThan(0, $actual);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage RoutCollection::remove is not implemented
     */
    public function testRemove()
    {
        $this->subject->remove('foo');
    }

    public function testAdd()
    {
        $route = new Route('/');

        $this->subject->add('foo', $route);

        $this->assertEquals($route, $this->subject->get('foo'));
    }
}
