<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Service;
use ProjectUrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

/**
 * @Service(public=false)
 */
class UrlMatcher
{

    /**
     * @param Request $request
     * @return array
     */
    public function match(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        // TODO fallback: SymfonyUrlMatcher
        include_once ROOT . 'cache/router_matcher.php';

        $matcher = new ProjectUrlMatcher($context);

        return $matcher->matchRequest($request);
    }
}
