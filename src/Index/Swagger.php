<?php

namespace BrainExe\Core\Index;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("Core.Index.Swagger")
 */
class Swagger
{

    use EventDispatcherTrait;

    /**
     * @return Response
     * @Route("/swagger.yml", name="swagger", methods="GET")
     */
    public function dump() : Response
    {
        $output = new BufferedOutput();
        $event  = new ConsoleEvent('swagger:dump', $output);

        $this->dispatchEvent($event);

        return new Response($output->fetch(), 200, [
            'Content-Type' => 'text/x-yaml'
        ]);
    }
}
