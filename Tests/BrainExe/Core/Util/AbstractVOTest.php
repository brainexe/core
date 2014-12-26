<?php

namespace Tests\BrainExe\Core\Util\AbstractVO;

use BrainExe\Core\Util\AbstractVO;
use PHPUnit_Framework_TestCase;

class TestVO extends AbstractVO
{
    public $test1;
    public $test2;
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
            'test1' => 1,
            'test2' => 2,
        ];
        $this->subject->fillValues($values);

        $expectedResult = new TestVO();
        $expectedResult->test1 = 1;
        $expectedResult->test2 = 2;

        $this->assertEquals($expectedResult, $this->subject);
    }
}
