<?php

namespace BrainExe\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use Symfony\Component\DependencyInjection\Container;

if (!defined('CORE_ROOT')) {
    define('CORE_ROOT', __DIR__);
}

if (!defined('ROOT')) {
    define('ROOT', realpath(CORE_ROOT . '/../').'/');
}

if (!defined('BRAINEXE_VENDOR_ROOT')) {
    define('BRAINEXE_VENDOR_ROOT', ROOT . 'vendor/brainexe/');
}

if (!defined('CORE_STANDALONE')) {
    define('CORE_STANDALONE', false);
}

class Core
{

    /**
     * @return Container
     */
    public function boot()
    {
        chdir(ROOT);
        umask(0);

        $files = glob('cache/dic_*.php');

        /** @var Container $dic */
        if ($files) {
            include_once $files[0];
            preg_match('/dic_([\d]*)/', $files[0], $matches);
            $class = $matches[0];
            $dic   = new $class();
        } else {
            $rebuild = new Rebuild();
            $dic = $rebuild->rebuildDIC(true);
        }

        date_default_timezone_set($dic->getParameter('timezone'));

        // TODO improve error logging
        $dic->get('monolog.ErrorHandler');

        return $dic;
    }
}
