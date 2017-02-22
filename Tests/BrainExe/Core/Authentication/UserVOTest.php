<?php

namespace Tests\BrainExe\Core\Authentication;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Authentication\UserVO
 */
class UserVOTest extends TestCase
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

        $actualRoles = $this->subject->getRoles();

        $this->assertEquals($this->subject->roles, $actualRoles);
    }

    public function testRolesAdmin()
    {
        $this->subject->roles = [
            UserVO::ROLE_ADMIN
        ];

        $this->assertTrue($this->subject->hasRole(UserVO::ROLE_USER));
        $this->assertTrue($this->subject->hasRole(UserVO::ROLE_ADMIN));
        $this->assertFalse($this->subject->hasRole('role_444'));
    }

    public function testRolesUser()
    {
        $this->subject->roles = [
            UserVO::ROLE_USER
        ];

        $this->assertTrue($this->subject->hasRole(UserVO::ROLE_USER));
        $this->assertFalse($this->subject->hasRole(UserVO::ROLE_ADMIN));
        $this->assertFalse($this->subject->hasRole('role_444'));
    }

    public function testGetProperties()
    {
        $this->subject->username = 'username';
        $this->subject->password_hash = 'password';
        $this->subject->id = 4141;

        $this->assertEquals('username', $this->subject->getUsername());
        $this->assertEquals('password', $this->subject->getPassword());
        $this->assertEquals(4141, $this->subject->getId());
    }

    public function testToJson()
    {
        $this->subject->username      = $username = 'username';
        $this->subject->id            = $userId = 42;
        $this->subject->password      = 'password';
        $this->subject->password_hash = 'password_hash';
        $this->subject->email         = 'email@localhost';

        $actualResult = $this->subject->jsonSerialize();

        $expectedResult = [
            'username' => $username,
            'userId' => $userId,
            'avatar' => null,
            'roles' => [],
            'email' => 'email@localhost'
        ];
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSalt()
    {
        $this->subject->username = $username = 'username';

        $this->assertEquals($username, $this->subject->getSalt());
    }
}
