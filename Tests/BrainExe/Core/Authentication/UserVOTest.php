<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @Covers BrainExe\Core\Authentication\UserVO
 */
class UserVOTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var UserVO
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new UserVO();
    }

    public function testRoles()
    {
        $this->subject->roles = [
        'role_1',
        'role_2'
        ];

        $this->assertTrue($this->subject->hasRole('role_1'));
        $this->assertTrue($this->subject->hasRole('role_2'));
        $this->assertFalse($this->subject->hasRole('role_444'));

        $actual_roles   = $this->subject->getRoles();
        $expected_roles = [
        new Role('role_1'),
        new Role('role_2'),
        ];

        $this->assertEquals($expected_roles, $actual_roles);
    }

    public function testToJson()
    {
        $this->subject->username      = $username = 'username';
        $this->subject->id            = $id = 42;
        $this->subject->password      = 'password';
        $this->subject->password_hash = 'password_hash';

        $actualResult = $this->subject->jsonSerialize();

        $expectedResult = [
        'username' => $username,
        'id' => $id,
        ];
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testPassword()
    {
        $this->subject->password = 'password';
        $this->subject->password_hash = 'password_hash';

        $this->subject->eraseCredentials();

        $this->assertNull($this->subject->password);
        $this->assertNull($this->subject->password_hash);
    }

    public function testGetSalt()
    {
        $this->subject->username = $username = 'username';

        $this->assertEquals($username, $this->subject->getSalt());
    }
}
