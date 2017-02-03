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

        $className = file_get_contents(ROOT . 'cache/dic.txt');
        if (!class_exists($className, false)) {
            include $fileName;
        }
        $dic = new $className();

        $dic->get('monolog.ErrorHandler');

        return $dic;
    }
}
