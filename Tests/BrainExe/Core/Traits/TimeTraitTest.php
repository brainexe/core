<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

class TimeTraitTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TimeTrait
     */
    private $subject;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockTime = $this->getMock(Time::class);

        $this->subject = $this->getMockForTrait(TimeTrait::class);
        $this->subject->setTime($this->mockTime);
    }

    public function testNow()
    {
        $now = 100;

        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $actualResult = $this->subject->now();

        $this->assertEquals($now, $actualResult);
    }

    public function testGetTime()
    {
        $actualResult = $this->subject->getTime();

        $this->assertEquals($this->mockTime, $actualResult);
    }
}
