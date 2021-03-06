<?php

namespace Tests\BrainExe\Core\Console;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Console\CreateRegisterLinkCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \BrainExe\Core\Console\CreateRegisterLinkCommand
 */
class CreateRegisterLinkCommandTest extends TestCase
{

    /**
     * @var CreateRegisterLinkCommand
     */
    private $subject;

    /**
     * @var RegisterTokens|MockObject
     */
    private $registerTokens;

    public function setUp()
    {
        $this->registerTokens = $this->createMock(RegisterTokens::class);

        $this->subject = new CreateRegisterLinkCommand($this->registerTokens);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $token = 11880;

        $this->registerTokens
            ->expects($this->once())
            ->method('addToken')
            ->willReturn($token);

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $expectedResult = sprintf("/register/?token=%s\n", $token);
        $this->assertEquals($expectedResult, $output);
    }
}
