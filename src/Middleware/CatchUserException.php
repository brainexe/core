<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\ServiceContainerTrait;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @Middleware("Middleware.CatchUserException")
 */
class CatchUserException extends AbstractMiddleware
{

    /**
     * {@inheritdoc}
     */
    public function processException(Request $request, Exception $exception)
    {
        if ($exception instanceof ResourceNotFoundException) {
            $exception = new UserException(sprintf('Page not found: %s', $request->getRequestUri()), 0, $exception);

            $response = new Response('', 404);
        } elseif ($exception instanceof MethodNotAllowedException) {
            $exception = new UserException('You are not allowed to access the page', 0, $exception);
            $response  = new Response('', 405);
        } elseif ($exception instanceof UserException) {
            // just pass a UserException to Frontend
            $response  = new Response('', 200);
        } else {
            $exception = new UserException($exception->getMessage(), 0, $exception);
            $response  = new Response('', 500);
        }

        /** @var Exception $exception */
        if ($request->isXmlHttpRequest()) {
            $message = $exception->getMessage() ?: _('An error occurred');
            $response->headers->set(
                'X-Flash',
                json_encode([ControllerInterface::ALERT_DANGER, $message])
            );
        } else {
            $responseString = $exception->getMessage();
            $response->setContent($responseString);
        }

        return $response;
    }
}
