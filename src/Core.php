<?php

namespace BrainExe\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use Symfony\Component\DependencyInjection\Container;

/**
 * @api
 */
class Core
{

    /**
     * @return Container
     */
    public function boot() : Container
    {
        $fileName = ROOT . 'cache/dic.php';
        /** @var Container $dic */
        if (!is_file($fileName)) {
            $rebuild = new Rebuild();
            $rebuild->buildContainer();
        }

        include_once $fileName;

        $dic = new \DumpedContainer();

        $dic->get('monolog.ErrorHandler');

        return $dic;
    }
}
