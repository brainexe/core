<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RebuildTest extends TestCase
{

    public function testRebuildContainer()
    {
        $subject = new Rebuild();

        $actualResult = $subject->rebuildDIC(false);

        $this->assertInstanceOf(ContainerBuilder::class, $actualResult);
    }

    public function testRebuildWithBootContainer()
    {
        $subject = new Rebuild();

        $actualResult = $subject->rebuildDIC(true);

        $this->assertInstanceOf(Container::class, $actualResult);
    }
}
