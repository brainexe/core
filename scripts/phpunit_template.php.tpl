<?php

namespace %test_namespace%;

%use_statements%

/**
 * @covers %service_namespace%
 */
class %class_name%Test extends TestCase
{

    /**
     * @var %class_name%
     */
    private $subject;
%mock_properties%
    public function setUp()
    {
%local_mocks%
        $this->subject = new %class_name%(%constructor_arguments%);
%setters%
    }
%default_tests%
}
