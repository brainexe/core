<?php

namespace Tests\BrainExe\Core\Application\ControllerResolver;

use BrainExe\Core\Application\ControllerResolver;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers BrainExe\Core\Application\ControllerResolver
 */
class ControllerResolverTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ControllerResolver
     */
    private $subject;

    /**
     * @var ObjectFinder|MockObject
     */
    private $mockObjectFinder;

    public function setUp()
    {
        $this->mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);

        $this->subject = new ControllerResolver();
        $this->subject->setObjectFinder($this->mockObjectFinder);
    }

    public function testGetController()
    {
        $request = new Request();
        $this->subject->getController($request);
    }

    public function testGetArguments()
    {
        $request = new Request();
        $controller = null;

        $request->attributes->set('key1', 'value1');
        $request->attributes->set('key2', 'value2');

        $actualResult = $this->subject->getArguments($request, $controller);

        $expectedResult = [
        $request, 'value1', 'value2'
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }
}
