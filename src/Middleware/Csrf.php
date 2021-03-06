<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\TimeTrait;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Csrf")
 */
class Csrf extends AbstractMiddleware
{

    const CSRF   = 'csrf';
    const HEADER = 'X-XSRF-TOKEN';
    const COOKIE = 'XSRF-TOKEN';

    const LIFETIME = 0;

    use IdGeneratorTrait;
    use TimeTrait;

    /**
     * @var string
     */
    private $newToken;

    /**
     * {@inheritdoc}
     * @throws MethodNotAllowedException
     */
    public function processRequest(Request $request, Route $route)
    {
        $givenToken = $request->headers->get(self::HEADER);

        $session = $request->getSession();
        $expectedToken = $session->get(self::CSRF);
        if ($request->isMethod('GET') && (!$route->hasOption(self::CSRF)) || $route->hasDefault('_guest')) {
            if (empty($expectedToken)) {
                $this->renewCsrfToken();
            }
            return;
        }

        if (empty($givenToken) || $givenToken !== $expectedToken) {
            throw new MethodNotAllowedException(['POST'], 'invalid CSRF token');
        }

        $this->generateNewTokenWhenNeeded($session);
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if ($this->newToken) {
            $session = $request->getSession();
            $session->set(self::CSRF, $this->newToken);
            $session->set('csrf_timestamp', $this->now());
            $response->headers->setCookie(
                new Cookie(
                    self::COOKIE,
                    $this->newToken,
                    0,
                    '/',
                    null,
                    false,
                    false
                )
            );
            $this->newToken = null;
        }
    }

    /**
     * @return void
     */
    private function renewCsrfToken()
    {
        $this->newToken = $this->generateRandomId(20);
    }

    /**
     * @param SessionInterface $session
     */
    private function generateNewTokenWhenNeeded(SessionInterface $session)
    {
        $now        = $this->now();
        $lastUpdate = $session->get('csrf_timestamp');
        if ($lastUpdate + self::LIFETIME < $now) {
            $this->renewCsrfToken();
        }
    }
}
