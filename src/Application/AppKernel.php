<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Middleware\MiddlewareInterface;
use Exception;
use Generator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @Service(public=true)
 */
class AppKernel implements HttpKernelInterface
{

    /**
     * @var ControllerResolver
     */
    private $resolver;

    /**
     * @var SerializedRouteCollection
     */
    private $routes;

    /**
     * @var UrlMatcher
     */
    private $urlMatcher;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @Inject({
     *     "@ControllerResolver",
     *     "@Core.RouteCollection",
     *     "@UrlMatcher",
     *     null
     * })
     * @param ControllerResolver $resolver
     * @param SerializedRouteCollection $routes
     * @param UrlMatcher $urlMatcher
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(
        ControllerResolver $resolver,
        SerializedRouteCollection $routes,
        UrlMatcher $urlMatcher,
        array $middlewares
    ) {
        $this->resolver    = $resolver;
        $this->routes      = $routes;
        $this->urlMatcher  = $urlMatcher;
        $this->middlewares = $middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $response = null;

        try {
            $response = $this->handleRequest($request);
        } catch (Exception $exception) {
            $response = $this->applyExceptionMiddleware($request, $exception);
        }

        $response = $this->prepareResponse($response);

        $this->applyResponseMiddleware($request, $response);

        return $response;
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    private function handleRequest(Request $request)
    {
        // match route and set attributes in request object
        $attributes = $this->urlMatcher->match($request);
        $request->attributes->replace($attributes);

        $routeName = $attributes['_route'];
        $route     = $this->routes->get($routeName);

        foreach ($this->middlewares as $middleware) {
            $response = $middleware->processRequest($request, $route);
            if ($response) {
                // e.g. RedirectResponse or rendered error page
                return $response;
            }
        }

        /** @var callable $callable */
        $callable  = $this->resolver->getController($request);
        $arguments = $this->resolver->getArguments($request, $callable);

        return $callable(...$arguments);
    }

    /**
     * @param Response|mixed $response
     * @return Response
     */
    private function prepareResponse($response) : Response
    {
        if ($response instanceof Generator) {
            $response = iterator_to_array($response);
        }

        if (!$response instanceof Response) {
            return new JsonResponse($response);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    protected function applyResponseMiddleware(Request $request, Response $response)
    {
        $middlewareIdx = count($this->middlewares) - 1;
        for ($i = $middlewareIdx; $i >= 0; $i--) {
            $middleware = $this->middlewares[$i];
            $middleware->processResponse($request, $response);
        }
    }

    /**
     * @param Request $request
     * @param Exception $exception
     * @return Response|null
     */
    protected function applyExceptionMiddleware(Request $request, Exception $exception)
    {
        foreach ($this->middlewares as $middleware) {
            $response = $middleware->processException($request, $exception);
            if ($response !== null) {
                return $response;
            }
        }

        return null;
    }
}
