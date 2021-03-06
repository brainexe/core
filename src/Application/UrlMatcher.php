<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Annotations\Service;
use ProjectUrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

/**
 * @Service
 */
class UrlMatcher
{

    /**
     * @param Request $request
     * @return array
     */
    public function match(Request $request) : array
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        include_once ROOT . 'cache/router_matcher.php';

        $matcher = new ProjectUrlMatcher($context);

        return $matcher->matchRequest($request);
    }
}
