<?php

namespace BrainExe\Tests\Core\Traits;

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class TimeTraitTest extends TestCase
{

    /**
     * @var TimeTrait
     */
    private $subject;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->time = $this->createMock(Time::class);

        $this->subject = $this->getMockForTrait(TimeTrait::class);
        $this->subject->setTime($this->time);
    }

    public function testNow()
    {
        $now = 100;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $actualResult = $this->subject->now();

        $this->assertEquals($now, $actualResult);
    }

    public function testGetTime()
    {
        $actualResult = $this->subject->getTime();

        $this->assertEquals($this->time, $actualResult);
    }
}
