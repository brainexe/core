<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\Core;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class CoreTest extends PHPUnit_Framework_TestCase
{

    public function testBoot()
    {
        $dic = Core::boot();

        $this->assertInstanceOf(Container::class, $dic);
    }
}
