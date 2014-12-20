<?php

namespace Tests\BrainExe\Core\Util\IdGenerator;

use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Util\IdGenerator
 */
class IdGeneratorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var IdGenerator
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new IdGenerator();
    }

    public function testGenerateRandomNumericId()
    {
        $actualResult = $this->subject->generateRandomNumericId();
        $actualResult2 = $this->subject->generateRandomNumericId();

        $this->assertInternalType('integer', $actualResult);
        $this->assertGreaterThan(0, $actualResult);

        $this->assertNotEquals($actualResult, $actualResult2);
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
