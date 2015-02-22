<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @Service(public=false)
 */
class ControllerResolver implements ControllerResolverInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject("@Service_container")
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller');

        list($serviceId, $method) = $controller;

        $service = $this->container->get($serviceId);

        return [$service, $method];
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(Request $request, $controller)
    {
        $arguments = [
            $request
        ];

        foreach ($request->attributes->all() as $attribute => $value) {
            if ($attribute[0] !== '_') {
                $arguments[] = $value;
            }
        }

        return $arguments;
    }
}
