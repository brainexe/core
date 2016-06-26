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
     * @var string[]
     */
    private $allowedUrls;

    /**
     * @var bool
     */
    private $forceHttps;

    /**
     * @Inject({"%application.allowed_urls%", "%application.force_https%"})
     * @param array $allowedUrls
     * @param bool $forceHttps
     */
    public function __construct(array $allowedUrls, bool $forceHttps)
    {
        $this->allowedUrls = $allowedUrls;
        $this->forceHttps  = $forceHttps;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if (!$request->isXmlHttpRequest()) {
            $response->headers->set('Content-Security-Policy', $this->getContentSecurityPolicy($request));
            $response->headers->set('X-Frame-Options', 'DENY');

            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000 ; includeSubDomains');
            }
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getContentSecurityPolicy(Request $request) : string
    {
        $allowed = [];
        $allowed[] = "'self'";

        foreach ($this->allowedUrls as $url) {
            $port = parse_url($url, PHP_URL_PORT);
            $host = parse_url($url, PHP_URL_HOST) ?: $request->getHost();

            if ($port) {
                $host .= ':' . $port;
            }
            $allowed[] = 'http://' . $host;
            $allowed[] = 'https://' . $host;
            $allowed[] = 'ws://' . $host;
        }

        $parts = [
            'default-src \'self\'',
            'style-src \'self\' \'unsafe-inline\'',
            sprintf('connect-src %s', implode(' ', $allowed)),
        ];

        return implode('; ', $parts);
    }
}
