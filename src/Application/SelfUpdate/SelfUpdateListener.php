<?php

namespace BrainExe\Core\Application\SelfUpdate;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class SelfUpdateListener implements EventSubscriberInterface {

    /**
     * @var SelfUpdate
     */
    private $_selfUpdate;

    /**
     * @inject("@SelfUpdate")
     * @param SelfUpdate $selfUpdate
     */
    public function __construct(SelfUpdate $selfUpdate) {
        $this->_selfUpdate = $selfUpdate;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [
            SelfUpdateEvent::TRIGGER => 'startSelfUpdate',
        ];
    }

    public function startSelfUpdate() {
		$this->_selfUpdate->startUpdate();
    }

}