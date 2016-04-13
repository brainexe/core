<?php

namespace Tests\BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Authentication\Controller\SettingsController;
use BrainExe\Core\Authentication\Settings\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers BrainExe\Core\Authentication\Controller\SettingsController
 */
class SettingsControllerTest extends TestCase
{

    /**
     * @var SettingsController
     */
    private $subject;

    /**
     * @var Settings|MockObject
     */
    private $settings;

    public function setUp()
    {
        $this->settings = $this->getMock(Settings::class, [], [], '', false);

        $this->subject = new SettingsController($this->settings);
    }

    public function testGetAll()
    {
        $request = new Request();
        $request->attributes->set('user_id', $userId = 42);

        $settings = [
            'key' => 'value'
        ];

        $this->settings
            ->expects($this->once())
            ->method('getAll')
            ->with($userId)
            ->willReturn($settings);

        $actual = $this->subject->all($request);

        $this->assertEquals($settings, $actual);
    }

    public function testSet()
    {
        $key   = 'myKey';
        $value = 'myValue';

        $request = new Request();
        $request->attributes->set('user_id', $userId = 42);
        $request->request->set('value', $value);

        $this->settings
            ->expects($this->once())
            ->method('set')
            ->with($userId, $key, $value);

        $actual = $this->subject->set($request, $key);

        $this->assertTrue($actual);
    }
}
