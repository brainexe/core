<?php

namespace BrainExe\Tests\Core\Translation;

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\Core\Translation\TranslationTrait;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class TranslationTraitTest extends TestCase
{

    /**
     * @var TranslationTrait
     */
    private $subject;

    public function setUp()
    {
        $this->subject = $this->getMockForTrait(TranslationTrait::class);
    }

    /**
     * @param string $base
     * @param array $parameters
     * @param string $expected
     * @dataProvider provideTranslationStrings
     */
    public function testTranslate(string $base, array $parameters, string $expected)
    {
        $actual = $this->subject->translate($base, ...$parameters);

        $this->assertEquals($expected, $actual);
    }

    public function provideTranslationStrings()
    {
        return [
            ['', [], ''],
            ['testString', [], 'testString'],
            ['testString %s', ['yes'], 'testString yes'],
            ['testString %s %d', ['yes', 123], 'testString yes 123'],
        ];
    }
}
