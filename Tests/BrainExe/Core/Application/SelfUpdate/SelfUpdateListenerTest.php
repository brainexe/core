<?php

namespace Tests\BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;

use BrainExe\Core\Application\SelfUpdate\SelfUpdate;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateListener;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Application\SelfUpdate\SelfUpdateListener
 */
class SelfUpdateListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SelfUpdateListener
     */
    private $subject;

    /**
     * @var SelfUpdate|MockObject
     */
    private $mockSelfUpdate;

    public function setUp()
    {
        $this->mockSelfUpdate = $this->getMock(SelfUpdate::class, [], [], '', false);

        $this->subject = new SelfUpdateListener($this->mockSelfUpdate);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testStartSelfUpdate()
    {
        $this->mockSelfUpdate
        ->expects($this->once())
        ->method('startUpdate');

        $this->subject->startSelfUpdate();
    }
}
