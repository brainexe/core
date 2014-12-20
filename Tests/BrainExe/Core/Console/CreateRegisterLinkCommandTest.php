<?php

namespace Tests\BrainExe\Core\Console\CreateRegisterLinkCommand;

use BrainExe\Core\Authentication\RegisterTokens;
use BrainExe\Core\Console\CreateRegisterLinkCommand;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers BrainExe\Core\Console\CreateRegisterLinkCommand
 */
class CreateRegisterLinkCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var CreateRegisterLinkCommand
     */
    private $subject;

    /**
     * @var RegisterTokens|MockObject
     */
    private $mockRegisterTokens;

    public function setUp()
    {
        $this->mockRegisterTokens = $this->getMock(RegisterTokens::class, [], [], '', false);

        $this->subject = new CreateRegisterLinkCommand($this->mockRegisterTokens);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $token = 11880;

        $this->mockRegisterTokens
        ->expects($this->once())
        ->method('addToken')
        ->will($this->returnValue($token));

        $commandTester->execute([]);
        $output = $commandTester->getDisplay();

        $expectedResult = sprintf("/register/?token=%s\n", $token);
        $this->assertEquals($expectedResult, $output);
    }
}
