<?php

namespace Tests\BrainExe\Core\Console\CreateUserCommand;

use BrainExe\Core\Authentication\Register;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Console\CreateUserCommand;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers BrainExe\Core\Console\CreateUserCommand
 */
class CreateUserCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CreateUserCommand
     */
    private $subject;

    /**
     * @var Register|MockObject
     */
    private $mockRegister;

    public function setUp()
    {
        $this->mockRegister = $this->getMock(Register::class, [], [], '', false);

        $this->subject = new CreateUserCommand($this->mockRegister);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $username = 'username';
        $password = 'password';
        $roles    = 'role1,role2';
        $user_id  = 42;

        $session = new Session(new MockArraySessionStorage());

        $user = new UserVO();
        $user->username = $username;
        $user->password = $password;
        $user->roles    = ['role1', 'role2'];

        $this->mockRegister
        ->expects($this->once())
        ->method('register')
        ->with($user, $session, null)
        ->will($this->returnValue($user_id));

        $commandTester->execute([
        'username' => $username,
        'password' => $password,
        'roles'    => $roles
        ]);

        $output = $commandTester->getDisplay();

        $this->assertEquals("New user-id: $user_id\n", $output);
    }
}
