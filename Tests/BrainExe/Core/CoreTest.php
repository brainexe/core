<?php

namespace BrainExe\Tests;

use BrainExe\Core\Core;
use PHPUnit\Framework\TestCase;
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
