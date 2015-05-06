<?php

namespace BrainExe\Core\Console\TestGenerator;

use BrainExe\Core\Console\TestCreateCommand;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @codeCoverageIgnore
 */
class ProcessMethod
{

    /**
     * @var TestCreateCommand
     */
    private $command;

    public function __construct(TestCreateCommand $command)
    {
        $this->command = $command;
    }

    /**
     * @param array $methodCall
     * @param TestData $testData
     */
    public function processMethod(array $methodCall, TestData $testData)
    {
        list ($setterName, $references) = $methodCall;

        /** @var Reference $reference */
        foreach ($references as $reference) {
            if (!$reference instanceof Reference) {
                continue;
            }

            $referenceServiceId = (string)$reference;

            if ('%' === substr($referenceServiceId, 0, 1)) {
                // add config setter with current config value
                $parameterName  = substr($reference, 1, -1);
                $parameterValue = $this->command->container->getParameter($parameterName);

                $formattedParameter = var_export($parameterValue, true);

                $testData->setterCalls[] = sprintf("\t\t\$this->subject->%s(%s);", $setterName, $formattedParameter);

            } else {
                // add setter for model mock
                $referenceService = $this->command->getServiceDefinition($referenceServiceId);
                $mockName         = $this->command->getShortClassName($referenceService->getClass());

                $testData->setterCalls[] = sprintf(
                    "\t\t\$this->subject->%s(\$this->%s);",
                    $setterName,
                    lcfirst($mockName)
                );
                $this->command->addMock($referenceService, $testData, $mockName);
            }
        }
    }
}
