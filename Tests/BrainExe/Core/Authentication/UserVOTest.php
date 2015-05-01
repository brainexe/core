<?php

namespace Tests\BrainExe\Core\Authentication\RegisterTokens;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @covers BrainExe\Core\Authentication\UserVO
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

        $actualRoles = $this->subject->getRoles();
            $expectedRoles = [
            new Role('role_1'),
            new Role('role_2'),
        ];

        $this->assertEquals($expectedRoles, $actualRoles);
    }

    public function testToJson()
    {
        $this->subject->username      = $username = 'username';
        $this->subject->id            = $userId = 42;
        $this->subject->password      = 'password';
        $this->subject->password_hash = 'password_hash';

        $actualResult = $this->subject->jsonSerialize();

        $expectedResult = [
            'username' => $username,
            'id' => $userId,
            'avatar' => null,
            'roles' => []
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
