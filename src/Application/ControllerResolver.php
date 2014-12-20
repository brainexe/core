<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Traits\ServiceContainerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @Service(public=false)
 */
class ControllerResolver implements ControllerResolverInterface
{

    use ServiceContainerTrait;

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller');

        list($service_id, $method) = $controller;

        $service = $this->getService($service_id);

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
