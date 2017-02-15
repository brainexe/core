<?php

namespace BrainExe\Core\Stats;


use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener("Stats.Listener")
 */
class Listener
{

    /**
     * @var Stats
     */
    private $stats;

    /**
     * @param Stats $stats
     */
    public function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * @Listen(Event::INCREASE)
     * @param Event $event
     */
    public function handleIncreaseEvent(Event $event)
    {
        $this->stats->increase([$event->getKey() => $event->getValue()]);
    }

    /**
     * @Listen(Event::SET)
     * @param Event $event
     */
    public function handleSetEvent(Event $event)
    {
        $this->stats->set([$event->getKey() => $event->getValue()]);
    }

    /**
     * @Listen(MultiEvent::INCREASE)
     * @param MultiEvent $event
     */
    public function handleMultiIncreaseEvent(MultiEvent $event)
    {
        $this->stats->increase($event->getValues());
    }

    /**
     * @Listen(MultiEvent::SET)
     * @param MultiEvent $event
     */
    public function handleMultiSetEvent(MultiEvent $event)
    {
        $this->stats->set($event->getValues());
    }
}
