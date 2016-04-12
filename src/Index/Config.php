<?php

namespace BrainExe\Core\Index;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("Core.Index.Config")
 */
class Config
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @Inject({"%config.public%"})
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed[]
     * @Route("/config/", name="config")
     * @Guest
     */
    public function config() : array
    {
        return $this->config;
    }
}
