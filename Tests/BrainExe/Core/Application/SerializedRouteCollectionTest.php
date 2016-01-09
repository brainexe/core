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
        $this->subject = new SerializedRouteCollection([
            'foo' => 'C:31:"Symfony\\Component\\Routing\\Route":348:{a:9:{s:4:"path";s:8:"/espeak/";s:4:"host";s:0:"";s:8:"defaults";a:1:{s:11:"_controller";a:2:{i:0;s:29:"__Controller.EspeakController";i:1;s:5:"index";}}s:12:"requirements";a:0:{}s:7:"options";a:1:{s:14:"compiler_class";s:39:"Symfony\\Component\\Routing\\RouteCompiler";}s:7:"schemes";a:0:{}s:7:"methods";a:0:{}s:9:"condition";s:0:"";s:8:"compiled";N;}}',
        ]);
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

        $this->assertEquals(1, $actual);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage RoutCollection::remove is not implemented
     */
    public function testRemove()
    {
        $this->subject->remove('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage RoutCollection::add is not implemented
     */
    public function testAdd()
    {
        $this->subject->add('foo', new Route('/'));
    }
}
