<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Inject;
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
    private $debug;

    /**
     * @Inject({"%application.allowed_urls%", "%debug%"})
     * @param array $allowedUrls
     * @param bool $debug
     */
    public function __construct(array $allowedUrls, bool $debug)
    {
        $this->allowedUrls = $allowedUrls;
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        if (!$request->isXmlHttpRequest()) {
            $response->headers->set('Content-Security-Policy', $this->getContentSecurityPolicy($request));
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-XSS-Protection', '1');

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
            $allowed[] = $host;
        }

        $parts = [
            sprintf('default-src \'self\''),
            'img-src *',
            'style-src \'self\' \'unsafe-inline\'',
            sprintf('connect-src \'self\' %s', implode(' ', $allowed)),
        ];

        if ($this->debug) {
            $parts[] = 'script * ';
        }

        return implode('; ', $parts);
    }
}
