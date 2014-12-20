<?php

namespace Tests\BrainExe\Core\Authentication\PasswordHasher;

use BrainExe\Core\Authentication\PasswordHasher;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Authentication\PasswordHasher
 */
class PasswordHasherTest extends PHPUnit_Framework_TestCase
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
        $password = 'password';

        $valid_hash = '$2y$07$bSguPj.ceocK7qSeYh9kS.d1ZgwRrcsoVBl.59dcLVy7Dwd3sQ8le';
        $invalid_hash = '$2y$10$lQfIxHU96vsdfsdfdsfsfggsfs.6';

        $actualResult = $this->subject->verifyHash($password, $valid_hash);
        $this->assertTrue($actualResult);

        $actualResult = $this->subject->verifyHash($password, $invalid_hash);
        $this->assertFalse($actualResult);
    }
}
