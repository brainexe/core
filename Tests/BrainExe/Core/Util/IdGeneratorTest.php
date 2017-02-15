<?php

namespace Tests\BrainExe\Core\Util;

use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use BrainExe\Core\Redis\Predis;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers \BrainExe\Core\Util\IdGenerator
 */
class IdGeneratorTest extends TestCase
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

        $this->subject = new IdGenerator(
            $this->redis
        );
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

    public function testGenerateRandomId()
    {
        $actualResult = $this->subject->generateRandomId(10);
        $actualResult2 = $this->subject->generateRandomId(10);

        $this->assertInternalType('string', $actualResult);
        $this->assertInternalType('string', $actualResult2);

        $this->assertEquals(10, strlen($actualResult));
        $this->assertEquals(10, strlen($actualResult2));

        $this->assertNotEquals($actualResult, $actualResult2);
    }
}
