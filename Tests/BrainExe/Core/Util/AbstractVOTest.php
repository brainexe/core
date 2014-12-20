<?php

namespace Tests\BrainExe\Core\Util\AbstractVO;

use BrainExe\Core\Util\AbstractVO;
use PHPUnit_Framework_TestCase;

class TestVO extends AbstractVO
{
    public $test_1;
    public $test_2;
}

/**
 * @Covers BrainExe\Core\Util\AbstractVO
 */
class AbstractVOTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var AbstractVO
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new TestVO();
    }

    public function testFillValues()
    {
        $values = [
        'test_1' => 1,
        'test_2' => 2,
        ];
        $this->subject->fillValues($values);

        $expectedResult = new TestVO();
        $expectedResult->test_1 = 1;
        $expectedResult->test_2 = 2;

        $this->assertEquals($expectedResult, $this->subject);
    }
}
