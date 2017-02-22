<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Annotations\Service;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @Service
 */
class ControllerResolver implements ControllerResolverInterface
{
    /**
     * @var ServiceLocator
     */
    private $controllers;

    /**
     * @param ServiceLocator $controllers
     */
    public function __construct(ServiceLocator $controllers)
    {
        $this->controllers = $controllers;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        list($serviceId, $method) = $request->attributes->get('_controller');

        $service = $this->controllers->get($serviceId);

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
