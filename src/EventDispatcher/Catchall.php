<?php

namespace BrainExe\Core\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

interface Catchall extends EventDispatcherInterface
{

    /**
     * @param SymfonyEventDispatcher $parent
     */
    public function setDispatcher(SymfonyEventDispatcher $parent);
}
