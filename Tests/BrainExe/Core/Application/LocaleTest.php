<?php

namespace Tests\BrainExe\Core\Application\Locale;

use BrainExe\Core\Application\Locale;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers BrainExe\Core\Application\Locale
 */
class LocaleTest extends TestCase
{

    /**
     * @var Locale
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Locale(['de', 'en']);
    }

    public function testGetLocales()
    {
        $actualResult = $this->subject->getLocales();
        $this->assertInternalType('array', $actualResult);
        $this->assertGreaterThan(0, count($actualResult));
    }
}
