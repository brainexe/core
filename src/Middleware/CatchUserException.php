<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Translation\TranslationTrait;
use Throwable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @Middleware("Middleware.CatchUserException")
 */
class CatchUserException extends AbstractMiddleware
{

    use LoggerTrait;
    use TranslationTrait;

    /**
     * {@inheritdoc}
     */
    public function processException(Request $request, Throwable $exception)
    {
        if ($exception instanceof ResourceNotFoundException) {
            $exception = new UserException(
                $this->translate(
                    'Page not found: %s',
                    htmlspecialchars($request->getRequestUri())
                ),
                0,
                $exception
            );
            $response  = new Response('', 404);
        } elseif ($exception instanceof MethodNotAllowedException) {
            $exception = new UserException(
                $this->translate(
                    'You are not allowed to access the page. Allowed methods: %s',
                    implode(',', $exception->getAllowedMethods())
                ),
                0,
                $exception
            );
            $response = new Response('', 405);
        } elseif ($exception instanceof UserException) {
            // just pass a UserException to Frontend
            $response  = new Response('', 200);
        } else {
            $exception = new UserException($exception->getMessage(), 0, $exception);
            $response  = new Response('', 500);
        }

        $this->error($exception->getMessage());
        $this->error($exception->getTraceAsString());

        $this->setMessage($exception, $response);

        return $response;
    }

    /**
     * @param Throwable $exception
     * @param Response $response
     */
    protected function setMessage(Throwable $exception, Response $response)
    {
        $message = $exception->getMessage() ?: $this->translate('An error occurred');
        $response->headers->set('X-Flash-Type', 'danger');
        $response->headers->set('X-Flash-Message', $message);
        $response->setContent($message);
    }
}
