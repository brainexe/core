<?php

namespace BrainExe\Core\Application\SelfUpdate;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class SelfUpdateListener implements EventSubscriberInterface
{

    /**
     * @var SelfUpdate
     */
    private $selfUpdate;

    /**
     * @Inject("@SelfUpdate")
     * @param SelfUpdate $selfUpdate
     */
    public function __construct(SelfUpdate $selfUpdate)
    {
        $this->selfUpdate = $selfUpdate;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SelfUpdateEvent::TRIGGER => 'startSelfUpdate',
        ];
    }

    public function startSelfUpdate()
    {
        $this->selfUpdate->startUpdate();
    }
}
