<?php

namespace Tests\BrainExe\Core\Authentication\Command;

use BrainExe\Core\Authentication\Command\CreateToken;
use BrainExe\Core\Authentication\Token;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \BrainExe\Core\Authentication\Command\CreateToken
 */
class CreateTokenTest extends TestCase
{

    /**
     * @var CreateToken
     */
    private $subject;

    /**
     * @var MockObject|Token
     */
    private $token;

    public function setUp()
    {
        $this->token = $this->createMock(Token::class);

        $this->subject = new CreateToken($this->token);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $userId = 1234;
        $roles  = 'foo,bar';
        $token  = 11880;

        $this->token
            ->expects($this->once())
            ->method('addToken')
            ->with($userId, ['foo', 'bar'])
            ->willReturn($token);

        $commandTester->execute(['user' => $userId, 'roles' => $roles]);
        $output = $commandTester->getDisplay();

        $expectedResult = sprintf("Created token %d for user 1234 with roles: foo,bar\n", $token);
        $this->assertEquals($expectedResult, $output);
    }
}
