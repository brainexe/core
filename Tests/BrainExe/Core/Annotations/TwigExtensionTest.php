<?php

namespace Tests\BrainExe\Core\Annotations;

use BrainExe\Core\Annotations\Builder\TwigExtension as Builder;
use BrainExe\Core\Annotations\TwigExtension as Annotation;
use Doctrine\Common\Annotations\Reader;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class TwigExtensionTest extends TestCase
{

    /**
     * @var Annotation
     */
    private $subject;

    public function __construct()
    {
        $this->subject = new Annotation([]);
    }

    public function testGetBuilder()
    {
        /** @var MockObject|Reader $reader */
        $reader = $this->getMock(Reader::class);

        $actualResult = $this->subject->getBuilder($reader);

        $this->assertInstanceOf(Builder::class, $actualResult);
    }
}
