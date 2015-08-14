<?php

namespace BrainExe\Core\Stats;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Stats.Listener")
 */
class Listener implements EventSubscriberInterface
{

    /**
     * @var Stats
     */
    private $stats;

    /**
     * @Inject({"@Stats.Stats"})
     * @param Stats $stats
     */
    public function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Event::INCREASE => 'handleIncreaseEvent',
            Event::SET      => 'handleSetEvent',
        ];
    }

    /**
     * @param Event $event
     */
    public function handleIncreaseEvent(Event $event)
    {
        $this->stats->increase($event->getKey(), $event->getValue());
    }

    /**
     * @param Event $event
     */
    public function handleSetEvent(Event $event)
    {
        $this->stats->set($event->getKey(), $event->getValue());
    }
}
