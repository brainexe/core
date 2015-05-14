<?php

namespace BrainExe\Core\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Routing\Annotation\Route as SymfonyRoute;

/**
 * @Annotation
 * @api
 */
class Route extends SymfonyRoute
{

    /**
     * @var bool
     */
    private $csrf = false;

    /**
     * @return bool
     */
    public function isCsrf()
    {
        return $this->csrf;
    }

    /**
     * @param bool $csrf
     */
    public function setCsrf($csrf)
    {
        $this->csrf = $csrf;
    }
}
