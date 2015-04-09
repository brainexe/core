<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\BackgroundEvent;
use BrainExe\Core\EventDispatcher\Catchall;
use BrainExe\InputControl\InputControlEvent;
use Exception;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @Service("Expression.Listener", public=false)
 */
class Listener extends EventDispatcher implements Catchall
{
    /**
     * @var Entity[]
     */
    private $expressions = [];

    /**
     * @var EventDispatcher
     */
    private $parent;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({"@Expression.Gateway", "@Expression.Language"})
     * @param Gateway $gateway
     * @param Language $language
     */
    public function __construct(Gateway $gateway, Language $language)
    {
        $this->gateway  = $gateway;
        $this->expressions  = $gateway->getAll();
        $this->language = $language;

        $this->language->register('setProperty', function () {
            throw new Exception('setProperty() not implemented');
        }, function ($parameters, $property, $value) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            $entity->payload ?: [];
            $entity->payload[$property] = $value;
        });

        $this->language->register('input', function () {
            throw new Exception('input() not implemented');
        }, function ($parameters, $string) {
            unset($parameters);
            $inputEvent = new InputControlEvent($string);
            $backgroundEvent = new BackgroundEvent($inputEvent);

            $this->parent->dispatch(BackgroundEvent::BACKGROUND, $backgroundEvent);
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
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        foreach ($this->expressions as $expression) {
            $parameters = [
                'event'     => $event,
                'eventName' => $eventName,
                'entity'    => $expression
            ];

            if ($this->language->evaluate($expression->condition, $parameters)) {
                foreach ($expression->actions as $action) {
                    $this->language->evaluate($action, $parameters);
                }

                $expression->counter++;

                $this->gateway->save($expression);
            }
        }
    }
}