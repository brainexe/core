<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Parameter")
 */
class Parameter extends AbstractMiddleware
{

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT']) && 'json' === $request->getContentType()) {
            $parameters = json_decode($request->getContent(), true);

            if (is_array($parameters)) {
                $request->request->replace($parameters);
            }
        }
    }
}
