<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\EventDispatcher\IntervalEvent;
use BrainExe\Core\Util\TimeParser;
use BrainExe\InputControl\Dispatcher;
use BrainExe\MessageQueue\MessageQueueGateway;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Expression.Controller")
 */
class Controller
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Dispatcher
     */
    private $inputControl;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var TimeParser
     */
    private $timeParser;
    /**
     * @var MessageQueueGateway
     */
    private $messageQueueGateway;

    /**
     * @Inject({
     *  "@Expression.Gateway",
     *  "@EventDispatcher",
     *  "@InputControl.Dispatcher",
     *  "@Expression.Language",
     *  "@TimeParser",
     *  "@MessageQueueGateway"
     * })
     * @param Gateway $gateway
     * @param EventDispatcher $dispatcher
     * @param Dispatcher $inputControl
     * @param Language $language
     * @param TimeParser $timeParser
     * @param MessageQueueGateway $messageQueueGateway
     */
    public function __construct(
        Gateway $gateway,
        EventDispatcher $dispatcher,
        Dispatcher $inputControl,
        Language $language,
        TimeParser $timeParser,
        MessageQueueGateway $messageQueueGateway
    ) {
        $this->gateway             = $gateway;
        $this->dispatcher          = $dispatcher;
        $this->inputControl        = $inputControl;
        $this->language            = $language;
        $this->timeParser          = $timeParser;
        $this->messageQueueGateway = $messageQueueGateway;
    }

    /**
     * @return Entity[]
     * @Route("/expressions/", name="expressions.load")
     */
    public function load()
    {
        return [
            'events'        => array_keys($this->dispatcher->getListeners()),
            'input_control' => array_keys($this->inputControl->getDefinedListeners()),
            'expressions'   => $this->gateway->getAll(),
            'timers'        => $this->messageQueueGateway->getEventsByType(IntervalEvent::INTERVAL)
        ];
    }

    /**
     * @Route("/expressions/save/", name="expressions.save")
     * @param Request $request
     * @return Entity
     * @throws UserException
     */
    public function save(Request $request)
    {
        $entity = new Entity();
        $entity->expressionId = $request->request->get('expressionId');
        $entity->condition    = $request->request->get('condition');
        $entity->actions      = (array)$request->request->get('actions');

        $this->validate($entity->condition);
        if (empty($entity->actions)) {
            throw new UserException(_('No actions defined'));
        }

        foreach ($entity->actions as $action) {
            $this->validate($action);
        }

        $this->gateway->save($entity);

        return $entity;
    }

    /**
     * @param Request $request
     * @Route("/expressions/delete/", name="expressions.delete")
     * @return bool
     */
    public function delete(Request $request)
    {
        $expressionId = $request->request->getInt('expressionId');

        $this->gateway->delete($expressionId);

        return true;
    }
    /**
     * @param Request $request
     * @Route("/expressions/timer/", name="expressions.timer")
     * @return bool
     */
    public function addTimer(Request $request)
    {
        $startTime = $request->request->get('startTime');
        $interval  = $request->request->getInt('interval');
        $timingId  = $request->request->get('timingId');

        $event = new IntervalEvent(
            new TimingEvent($timingId),
            $this->timeParser->parseString($startTime) ?: time(),
            $interval
        );

        $this->dispatcher->dispatchEvent($event);

        return [
            'timers' => $this->messageQueueGateway->getEventsByType(IntervalEvent::INTERVAL)
        ];
    }

    /**
     * @param string $expression
     */
    private function validate($expression)
    {
        $this->language->parse($expression, [
            'eventName',
            'event',
            'entity'
        ]);
    }

}
