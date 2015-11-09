<?php

namespace Tests\BrainExe\Core\Util\IdGenerator;

use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use BrainExe\Core\Redis\Predis;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers BrainExe\Core\Util\IdGenerator
 */
class IdGeneratorTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var IdGenerator
     */
    private $subject;

    /**
     * @var MockObject|Predis
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new IdGenerator();
        $this->subject->setRedis($this->redis);
    }

    public function testGenerateUniqueId()
    {
        $this->redis
            ->expects($this->once())
            ->method('incr')
            ->willReturn($expected = 100);

        $actual  = $this->subject->generateUniqueId();

        $this->assertEquals($expected, $actual);
    }
}
