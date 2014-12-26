<?php

namespace BrainExe\Tests\Core\Application;

use BrainExe\Core\Application\ErrorView;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\AnonymusUserVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ErrorViewTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ErrorView
     */
    private $subject;

    /**
     * @var bool
     */
    private $debug = true;

    /**
     * @var string
     */
    private $errorTemplate = 'error.html.twig';

    /**
     * @var MockObject|Twig_Environment
     */
    private $mockTwig;

    public function setup()
    {
        $this->mockTwig = $this->getMock('Twig_Environment');

        $this->subject = new ErrorView($this->debug, $this->errorTemplate);
        $this->subject->setTwig($this->mockTwig);
    }

    public function testRender()
    {
        $exception       = new UserException('Test-Exception');
        $request         = new Request();
        $expectedContent = 'Exception...';
        $currentUser     = new AnonymusUserVO();

        $this->mockTwig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->errorTemplate,
                [
                    'debug' => $this->debug,
                    'exception' => $exception,
                    'request' => $request,
                    'current_user' => $currentUser
                ]
            )
            ->willReturn($expectedContent);

        $response = $this->subject->renderException($request, $exception);

        $this->assertEquals($expectedContent, $response);
    }
}
