<?php

namespace Matze\Core\Application\SelfUpdate;

use Matze\Core\EventDispatcher\AbstractEventListener;

/**
 * @EventListener
 */
class SelfUpdateListener extends AbstractEventListener {

    /**
     * @{@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [
            SelfUpdateEvent::TRIGGER => 'startSelfUpdate',
        ];
    }

    public function startSelfUpdate() {
		/** @var SelfUpdate $self_update */
        $self_update = $this->getService('SelfUpdate');
		$self_update->startUpdate();
    }

}