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

    const CSRF   = 'csrf';
    const HEADER = 'X-XSRF-TOKEN';
    const COOKIE = 'XSRF-TOKEN';

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
        $givenToken = $request->headers->get(self::HEADER);

        if ($request->isMethod('GET') && !$route->hasOption(self::CSRF)) {
            if (empty($givenToken)) {
                $this->renewCsrfToken();
            }
            return;
        }

        $expectedToken = $request->getSession()->get(self::CSRF);

        if (empty($givenToken) || $givenToken !== $expectedToken) {
            throw new MethodNotAllowedException(['POST'], "invalid CSRF token");
        }

        // for the next request we expect a new token
        $this->renewCsrfToken();
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if ($this->newToken) {
            $request->getSession()->set(self::CSRF, $this->newToken);
            $response->headers->setCookie(new Cookie(self::COOKIE, $this->newToken, 0, '/', null, false, false));
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
