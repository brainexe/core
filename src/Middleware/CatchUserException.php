<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Translation\TranslationTrait;
use Monolog\Logger;
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

    const ERROR_NOT_AUTHORIZED = 'NotAuthorized';

    use TranslationTrait;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @Inject("@logger")
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

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
            $response->headers->set('X-Error', self::ERROR_NOT_AUTHORIZED);
        } elseif ($exception instanceof UserException) {
            // just pass a UserException to Frontend
            $response  = new Response('', 200);
        } else {
            $exception = new UserException($exception->getMessage(), 0, $exception);
            $response  = new Response('', 500);
        }

        $this->logger->error($exception->getMessage());
        $this->logger->error($exception->getTraceAsString());

        $this->setMessage($exception, $request, $response);

        return $response;
    }

    /**
     * @param Throwable $exception
     * @param Request $request
     * @param Response $response
     */
    private function setMessage(Throwable $exception, Request $request, Response $response)
    {
        $message = $exception->getMessage() ?: $this->translate('An error occurred');

        if ($request->isXmlHttpRequest()) {
            $response->headers->set('X-Flash-Type', 'danger');
            $response->headers->set('X-Flash-Message', $message);
        } else {
            $response->setContent($message);
        }
    }
}
