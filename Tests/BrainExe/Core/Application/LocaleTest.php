<?php

namespace Tests\BrainExe\Core\Application\Locale;

use BrainExe\Core\Application\Locale;
use PHPUnit_Framework_TestCase;

/**
 * @Covers BrainExe\Core\Application\Locale
 */
class LocaleTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Locale
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Locale();
    }

    public function testGetLocales()
    {
        $actualResult = $this->subject->getLocales();
        $this->assertInternalType('array', $actualResult);
        $this->assertGreaterThan(0, count($actualResult));
    }
}
