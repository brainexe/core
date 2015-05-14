<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Csrf", priority=4)
 */
class Csrf extends AbstractMiddleware
{

    const CSRF = 'csrf';

    use IdGeneratorTrait;

    /**
     * @var string
     */
    private $newToken = null;

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        $givenToken = $request->cookies->get(self::CSRF);

        if (empty($givenToken)) {
            $this->renewCsrfToken();
        }

        if (!$request->isMethod('POST') && !$route->hasOption(self::CSRF)) {
            return;
        }

        $expectedToken = $request->getSession()->get(self::CSRF);

        $this->renewCsrfToken();

        if (empty($givenToken) || $givenToken !== $expectedToken) {
            throw new MethodNotAllowedException(['POST'], "invalid CSRF token");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if ($this->newToken) {
            $request->getSession()->set(self::CSRF, $this->newToken);
            $response->headers->setCookie(new Cookie(self::CSRF, $this->newToken));
            $this->newToken = null;
        }
    }

    /**
     * @return void
     */
    private function renewCsrfToken()
    {
        $this->newToken = $this->generateRandomId();
    }
}
