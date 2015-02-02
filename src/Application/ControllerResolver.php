<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\DependencyInjection\ObjectFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * @Service(public=false)
 */
class ControllerResolver implements ControllerResolverInterface
{

    /**
     * @var ObjectFinder
     */
    private $objectFinder;

    /**
     * @Inject("@ObjectFinder")
     * @param ObjectFinder $objectFinder
     */
    public function setObjectFinder(ObjectFinder $objectFinder)
    {
        $this->objectFinder = $objectFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller');

        list($serviceId, $method) = $controller;

        $service = $this->objectFinder->getService($serviceId);

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
