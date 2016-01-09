<?php

namespace BrainExe\Tests;

use BrainExe\Core\Core;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

class CoreTest extends TestCase
{

    public function testBoot()
    {
        $core = new Core();
        $dic = $core->boot();

        $this->assertInstanceOf(Container::class, $dic);
    }
}
