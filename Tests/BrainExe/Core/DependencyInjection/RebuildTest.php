<?php

namespace BrainExe\Tests\Core;

use BrainExe\Core\DependencyInjection\Rebuild;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RebuildTest extends PHPUnit_Framework_TestCase
{

    public function testRebuildContainer()
    {
        $subject = new Rebuild();

        $actualResult = $subject->rebuildDIC(false);

        $this->assertInstanceOf(ContainerBuilder::class, $actualResult);
    }

    public function testRebuildWithBootContainer()
    {
        $this->markTestIncomplete();
        $subject = new Rebuild();

        $actualResult = $subject->rebuildDIC(true);

        $this->assertInstanceOf(Container::class, $actualResult);
    }
}
