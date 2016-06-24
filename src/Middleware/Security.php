<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware("Middleware.Security")
 */
class Security extends AbstractMiddleware
{

    /**
     * @var string
     */
    private $socketUrl;

    /**
     * @Inject("%socket.url%")
     * @param $socketHost
     */
    public function __construct(string $socketHost)
    {
        $this->socketUrl = $socketHost;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if (!$request->isXmlHttpRequest()) {
            $response->headers->set('Content-Security-Policy', $this->getContentSecurityPolicy());
            $response->headers->set('X-Frame-Options', 'DENY');

            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000 ; includeSubDomains');
            }
        }
    }

    /**
     * @return string
     */
    protected function getContentSecurityPolicy() : string
    {
        $parts = [
            'default-src \'self\'',
            'style-src \'self\' \'unsafe-inline\'',
            sprintf('connect-src \'self\' %s', $this->socketUrl),
        ];

        return implode('; ', $parts);
    }
}
