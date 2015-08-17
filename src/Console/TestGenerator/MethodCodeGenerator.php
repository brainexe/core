<?php

namespace BrainExe\Core\Console\TestGenerator;

use ReflectionMethod;
use ReflectionParameter;

/**
 * @codeCoverageIgnore
 */
class MethodCodeGenerator
{
    /**
     * Generated dummy test code for a given service method
     *
     * @param TestData $data
     * @param ReflectionMethod $method
     * @param string $shortClassName
     * @return string
     */
    public function getDummyTestCode(TestData $data, ReflectionMethod $method, $shortClassName)
    {
        $methodName = $method->getName();

        $parameterList = [];
        $variableList  = [];

        foreach ($method->getParameters() as $parameter) {
            $parameterList[] = $variableName = sprintf('$' . $parameter->getName());

            $this->processMethod($data, $shortClassName, $parameter, $variableName, $variableList);
        }

        $code = sprintf("\tpublic function test%s()\n\t{\n", ucfirst($methodName));
        $code .= "\t\t\$this->markTestIncomplete('This is only a dummy implementation');\n\n";

        $code .= implode("\n", $variableList) . "\n";
        $parameterString = implode(', ', $parameterList);

        $hasReturnValue = strpos($method->getDocComment(), '@return') !== false;
        if ($hasReturnValue) {
            $code .= sprintf("\t\t\$actual = \$this->subject->%s(%s);\n", $methodName, $parameterString);
        } else {
            $code .= sprintf("\t\t\$this->subject->%s(%s);\n", $methodName, $parameterString);
        }

        $code .= "\t}\n";

        return $code;
    }

    /**
     * @param TestData $data
     * @param string $shortClassName
     * @param ReflectionParameter $parameter
     * @param string $variableName
     * @param array $variableList
     */
    private function processMethod(
        TestData $data,
        $shortClassName,
        ReflectionParameter $parameter,
        $variableName,
        &$variableList
    ) {
        $value = 'null';
        if ($parameter->isOptional()) {
            $value = $parameter->getDefaultValue();
        }

        if ($parameter->getClass()) {
            $class = $parameter->getClass()->name;
            $data->addUse($class);
            $value = sprintf('new %s()', $shortClassName);
        }

        $variableList[] = sprintf("\t\t%s = %s;", $variableName, $value);
    }
}
