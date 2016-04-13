<?php

namespace BrainExe\Core\Logger;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Core.Logger.Controller")
 */
class Controller
{

    use LoggerTrait;

    /**
     * @Route("/log/error/", name="log.error", methods="POST")
     * @Guest
     * @param Request $request
     * @return bool
     */
    public function logFrontend(Request $request)
    {
        $message = $request->request->get('message');

        $this->error($message, ['channel' => 'frontend_error']);

        return true;
    }
}
