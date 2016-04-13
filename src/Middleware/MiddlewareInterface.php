<?php

namespace BrainExe\Core\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Throwable;

interface MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Route $route
     * @return Response|null $response
     */
    public function processRequest(Request $request, Route $route);

    /**
     * @param Request $request
     * @param Response $response
     */
    public function processResponse(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return Response|void
     */
    public function processException(Request $request, Throwable $exception);
}
