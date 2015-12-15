<?php

namespace BrainExe\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use Symfony\Component\DependencyInjection\Container;

if (!defined('CORE_ROOT')) {
    define('CORE_ROOT', __DIR__);
}

if (!defined('ROOT')) {
    define('ROOT', realpath(CORE_ROOT . '/../') . '/');
}

if (!defined('BRAINEXE_VENDOR_ROOT')) {
    define('BRAINEXE_VENDOR_ROOT', ROOT . 'vendor/brainexe/');
}

/**
 * @api
 */
class Core
{

    /**
     * @return Container
     */
    public function boot()
    {
        chdir(ROOT);
        umask(0);

        $fileName = 'cache/dic.php';
        /** @var Container $dic */
        if (is_file($fileName)) {
            $className = file_get_contents('cache/dic.txt');
            if (!class_exists($className, false)) {
                include $fileName;
            }
            $dic = new $className();
        } else {
            $rebuild = new Rebuild();
            $dic = $rebuild->rebuildDIC(true);
        }

        date_default_timezone_set($dic->getParameter('timezone'));

        $dic->get('monolog.ErrorHandler');

        return $dic;
    }
}
