<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Traits\TwigTrait;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Service
 */
class ErrorView
{

    use TwigTrait;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var string
     */
    private $valueErrorTemplate;

    /**
     * @Inject({"%debug%", "%application.error_template%"})
     * @param boolean $debug
     * @param string $errorTemplate
     */
    public function __construct($debug, $errorTemplate)
    {
        $this->debug              = $debug;
        $this->valueErrorTemplate = $errorTemplate;
    }

    /**
     * @param Request $request
     * @param Exception $exception
     * @return string
     */
    public function renderException(Request $request, Exception $exception)
    {
        $content = $this->render($this->valueErrorTemplate, [
            'exception'    => $exception,
            'debug'        => $this->debug,
            'request'      => $request,
            'current_user' => $request->attributes->get('user') ?: new AnonymusUserVO(),
        ]);

        return $content;
    }
}
