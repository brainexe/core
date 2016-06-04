<?php

namespace Tests\BrainExe\Core\Authentication\Command;

use BrainExe\Core\Authentication\Command\ListTokens;
use BrainExe\Core\Authentication\Token;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers BrainExe\Core\Authentication\Command\ListTokens
 */
class ListTokensTest extends TestCase
{

    /**
     * @var ListTokens
     */
    private $subject;

    /**
     * @var MockObject|Token
     */
    private $token;

    public function setUp()
    {
        $this->token = $this->createMock(Token::class);

        $this->subject = new ListTokens($this->token);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $userId = 1234;

        $tokens = [
            '0815' => ['login'],
            '0816' => ['login', 'admin']
        ];

        $this->token
            ->expects($this->once())
            ->method('getTokensForUser')
            ->with($userId)
            ->willReturn($tokens);

        $commandTester->execute(['user' => $userId]);
        $output = $commandTester->getDisplay();

        $expectedResult = "+-------+-------------+
| token | roles       |
+-------+-------------+
| 0815  | login       |
| 0816  | login,admin |
+-------+-------------+\n";
        $this->assertEquals($expectedResult, $output);
    }
}
