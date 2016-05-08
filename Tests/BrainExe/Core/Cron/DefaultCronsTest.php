<?php

namespace BrainExe\Tests\Core\Cron;

use BrainExe\Core\Cron\DefaultCrons;
use PHPUnit_Framework_TestCase as TestCase;

class DefaultCronsTest extends TestCase
{

    /**
     * @var DefaultCrons
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new DefaultCrons();
    }

    public function testGetDefaults()
    {
        $this->assertNotEmpty($this->subject->getCrons());
    }
}
