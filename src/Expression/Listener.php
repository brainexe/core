<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\Catchall;
use BrainExe\InputControl\InputControlEvent;
use Exception;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @Service("Expression.Listener")
 */
class Listener extends EventDispatcher implements Catchall
{

    /**
     * @var string[]
     */
    private $trigger = [];

    /**
     * @var EventDispatcher
     */
    private $parent;

    public function __construct()
    {
        $this->register(
            'eventName == "sensor.value"',
            'sprintf("say Sensor %s %s", event.sensorVo.name, event.valueFormatted)'
        );

        $this->expression = new ExpressionLanguage();
        $this->expression->register('sprintf', function () {
            throw new Exception('sprintf() not implemented');
        }, function ($parameters, $string) {
            unset($parameters);
            return vsprintf($string, array_slice(func_get_args(), 2));
        });
    }

    /**
     * @param EventDispatcher $parent
     */
    public function setDispatcher(EventDispatcher $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param $condition
     * @param $action
     */
    public function register($condition, $action)
    {
        $this->trigger[$condition] = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        foreach ($this->trigger as $condition => $trigger) {
            $parameters = [
                'event'     => $event,
                'eventName' => $eventName
            ];

            if ($this->expression->evaluate($condition, $parameters)) {
                $triggerString = $this->expression->evaluate($trigger, $parameters);

                $inputEvent = new InputControlEvent($triggerString);
                $backgroundEvent = new BackgroundEvent($inputEvent);
                $this->parent->dispatch(BackgroundEvent::BACKGROUND, $backgroundEvent);
            }
        }

    }
}
