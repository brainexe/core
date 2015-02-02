<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\Core;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class CoreTest extends PHPUnit_Framework_TestCase
{

    public function testBoot()
    {
        $core = new Core();
        $dic = $core->boot();

        $this->assertInstanceOf(Container::class, $dic);
    }
}
