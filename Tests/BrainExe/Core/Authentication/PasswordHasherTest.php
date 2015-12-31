<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\PasswordHasher;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Authentication\PasswordHasher
 */
class PasswordHasherTest extends TestCase
{

    /**
     * @var PasswordHasher
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new PasswordHasher();
    }

    public function testGenerateHash()
    {
        $password = 'password';

        $actualResult1 = $this->subject->generateHash($password);
        $actualResult2 = $this->subject->generateHash($password);

        $this->assertInternalType('string', $actualResult1);
        $this->assertInternalType('string', $actualResult2);

        $this->assertNotEquals($actualResult1, $actualResult2);
    }

    public function testVerifyHash()
    {
        $password    = 'password';
        $validHash   = '$2y$07$bSguPj.ceocK7qSeYh9kS.d1ZgwRrcsoVBl.59dcLVy7Dwd3sQ8le';
        $invalidHash = '$2y$10$lQfIxHU96vsdfsdfdsfsfggsfs.6';

        $actualResult = $this->subject->verifyHash($password, $validHash);
        $this->assertTrue($actualResult);

        $actualResult = $this->subject->verifyHash($password, $invalidHash);
        $this->assertFalse($actualResult);
    }
}
