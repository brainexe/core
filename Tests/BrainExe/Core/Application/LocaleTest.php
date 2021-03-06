<?php

namespace Tests\BrainExe\Core\Application;

use BrainExe\Core\Application\Locale;
use PHPUnit\Framework\TestCase;

/**
 * @covers \BrainExe\Core\Application\Locale
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
