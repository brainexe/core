<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;

/**
 * @EventListener
 */
class Listener
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @Inject("@Console")
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @Listen(ConsoleEvent::NAME)
     * @param ConsoleEvent $event
     * @throws Exception
     */
    public function handleEvent(ConsoleEvent $event)
    {
        $input = new StringInput($event->getCommand());
        $this->application->setAutoExit(false);

        $this->application->run($input, $event->getOutput());
    }
}
