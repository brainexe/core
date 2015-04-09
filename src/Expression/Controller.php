<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Dispatcher;
use Symfony\Component\EventDispatcher\Event;
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
     * @Inject({
     *  "@Expression.Gateway",
     *  "@EventDispatcher",
     *  "@InputControl.Dispatcher",
     *  "@Expression.Language"
     * })
     * @param Gateway $gateway
     * @param EventDispatcher $dispatcher
     * @param Dispatcher $inputControl
     * @param Language $language
     */
    public function __construct(
        Gateway $gateway,
        EventDispatcher $dispatcher,
        Dispatcher $inputControl,
        Language $language
    ) {
        $this->gateway      = $gateway;
        $this->dispatcher   = $dispatcher;
        $this->inputControl = $inputControl;
        $this->language     = $language;
    }

    /**
     * @return Entity[]
     * @Route("/expressions/", name="expressions.load")
     */
    public function load()
    {
        return [
            'events'      => array_keys($this->dispatcher->getListeners()),
            'actions'     => array_keys($this->inputControl->getDefinedListeners()),
            'expressions' => $this->gateway->getAll()
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
