<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Console\TestGenerator\ProcessMethod;
use BrainExe\Core\Console\TestGenerator\TestData;
use BrainExe\Core\DependencyInjection\Rebuild;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

require_once "TestGenerator/TestData.php";

/**
 * @CommandAnnotation
 * @codeCoverageIgnore
 */
class TestCreateCommand extends Command
{

    /**
     * Cached container builder
     * @var ContainerBuilder|null
     */
    public $container = null;

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('test:create')
            ->addArgument('service', InputArgument::REQUIRED, 'service id, e.g. IndexController')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'only display the generated test');
    }

    /**
     * @Inject("@Core.Rebuild")
     * @param Rebuild $rebuild
     */
    public function __construct(Rebuild $rebuild)
    {
        $this->rebuild = $rebuild;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initContainerBuilder();

        $serviceId = $input->getArgument('service');

        $serviceObject     = $this->getService($serviceId);
        $serviceDefinition = $this->getServiceDefinition($serviceId);

        $serviceReflection    = new ReflectionClass($serviceObject);
        $serviceFullClassName = $serviceReflection->getName();
        $serviceClassName     = $this->getShortClassName($serviceFullClassName);

        $testData = new TestData();

        $testData->addUse(PHPUnit_Framework_TestCase::class, 'TestCase');
        $testData->addUse(PHPUnit_Framework_MockObject_MockObject::class, 'MockObject');
        $testData->addUse($serviceFullClassName);

        $blacklistedMethods = $this->getBlacklistedMethods($serviceReflection);

        $methods = $serviceReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $methodName = $method->getName();

            if (in_array($methodName, $blacklistedMethods)) {
                continue;
            }

            if ($method->getDeclaringClass() == $serviceReflection) {
                $testData->defaultTests[] = $this->getDummyTestCode($testData, $method);
            }
        }

        $this->setupConstructor($serviceDefinition, $testData);

        $methodProcessor = new ProcessMethod($this);
        foreach ($serviceDefinition->getMethodCalls() as $methodCall) {
            $methodProcessor->processMethod($methodCall, $testData);
        }

        $template = file_get_contents(CORE_ROOT . '/../scripts/phpunit_template.php.tpl');
        $template = str_replace(
            '%test_namespace%',
            $this->getTestNamespace($serviceReflection->getNamespaceName()),
            $template
        );
        $template = str_replace('%service_namespace%', $serviceFullClassName, $template);
        $template = str_replace('%class_name%', $serviceClassName, $template);
        $template = str_replace('%setters%', implode("\n", $testData->setterCalls), $template);
        $template = str_replace('%default_tests%', implode("\n", $testData->defaultTests), $template);
        $template = str_replace('%mock_properties%', implode("\n", $testData->mockProperties), $template);
        $template = str_replace('%use_statements%', $testData->renderUse(), $template);
        $template = str_replace('%local_mocks%', implode("\n", $testData->localMocks), $template);
        $template = str_replace('%constructor_arguments%', implode(", ", $testData->constructorArguments), $template);

        $testFileName = $this->getTestFileName($serviceFullClassName);

        if ($input->getOption('dry')) {
            $output->writeln($template);
            return;
        }

        // handle already existing test file
        if (file_exists($testFileName)) {
            $template = $this->handleExistingFile($input, $output, $serviceId, $testFileName, $template);
            if ($template === false) {
                return;
            }
        }

        $testDir = dirname($testFileName);

        if (!is_dir($testDir)) {
            mkdir($testDir, 0777, true);
        }

        file_put_contents($testFileName, $template);

        $output->writeln(
            sprintf("Created Test for '<info>%s</info>' in <info>%s</info>", $serviceId, $testFileName)
        );
    }

    /**
     * @param string $serviceNamespace
     * @return string
     */
    private function getTestFileName($serviceNamespace)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $serviceNamespace);

        return sprintf('%sTests/%sTest.php', ROOT, $path);
    }

    /**
     * @param string $serviceId
     * @return Definition
     */
    public function getServiceDefinition($serviceId)
    {
        return $this->container->getDefinition($serviceId);
    }

    /**
     * @param string $serviceId
     * @return object
     */
    public function getService($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @param string $serviceNamespace
     * @return string
     */
    private function getTestNamespace($serviceNamespace)
    {
        return "Tests\\" . $serviceNamespace;
    }

    /**
     * Generated dummy test code for a given service method
     *
     * @param TestData $data
     * @param ReflectionMethod $method
     * @return string
     */
    private function getDummyTestCode(TestData $data, ReflectionMethod $method)
    {
        $methodName = $method->getName();

        $parameterList = [];
        $variableList  = [];

        foreach ($method->getParameters() as $parameter) {
            $parameterList[] = $variableName = sprintf('$' . $parameter->getName());

            $value = 'null';
            if ($parameter->isOptional()) {
                $value = $parameter->getDefaultValue();
            }
            if ($parameter->getClass()) {
                $class = $parameter->getClass()->name;
                $data->addUse($class);
                $value = sprintf('new %s()', $this->getShortClassName($class));
            }

            $variableList[] = sprintf("\t\t%s = %s;", $variableName, $value);
        }

        $code = sprintf("\tpublic function test%s() {\n", ucfirst($methodName));
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

    private function initContainerBuilder()
    {
        if ($this->container !== null) {
            return;
        }

        $this->container = $this->rebuild->rebuildDIC(false);
    }

    /**
     * @param string
     * @return string
     */
    public function getShortClassName($fullClassName)
    {
        // Strip off namespace
        $lastBackslashPos = strrpos($fullClassName, '\\');

        if (!$lastBackslashPos) {
            return $fullClassName;
        }

        return substr($fullClassName, $lastBackslashPos + 1);
    }

    /**
     * @param ReflectionClass $serviceReflection
     * @return string[]
     */
    private function getBlacklistedMethods(ReflectionClass $serviceReflection)
    {
        $blacklistedMethods = [];

        foreach ($serviceReflection->getTraitNames() as $trait) {
            $reflection = new ReflectionClass($trait);
            foreach ($reflection->getMethods() as $method) {
                $blacklistedMethods[] = $method->getName();
            }
        }

        $blacklistedMethods[] = '__construct';

        return $blacklistedMethods;
    }

    /**
     * @param Definition $referenceService
     * @param TestData $testData
     * @param string $mockName
     */
    public function addMock(Definition $referenceService, TestData $testData, $mockName)
    {
        $class = $referenceService->getClass();
        $testData->addUse($class);

        $reflection  = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor && $constructor->getNumberOfParameters()) {
            $mock = sprintf(
                "\t\t\$this->%s = \$this->getMock(%s::class, [], [], '', false);",
                lcfirst($mockName),
                $mockName
            );
        } else {
            $mock = sprintf("\t\t\$this->%s = \$this->getMock(%s::class);", lcfirst($mockName), $mockName);
        }

        $testData->localMocks[]     = $mock;
        $testData->mockProperties[] = sprintf(
            "\t/**\n\t * @var %s|MockObject\n\t */\n\tprivate \$%s;\n",
            $mockName,
            lcfirst($mockName)
        );
    }

    /**
     * @param Definition $serviceDefinition
     * @param TestData $data
     */
    private function setupConstructor(Definition $serviceDefinition, TestData $data)
    {
        foreach ($serviceDefinition->getArguments() as $reference) {
            if ($reference instanceof Definition) {
                $definition = $reference;
                $mockName = $this->getShortClassName($definition->getClass());
            } elseif ($reference instanceof Reference) {
                // add setter for model mock
                $definition = $this->getServiceDefinition((string)$reference);
                $mockName = $this->getShortClassName($definition->getClass());
            } else {
                $data->constructorArguments[] = var_export($reference, true);
                continue;
            }

            $data->constructorArguments[] = sprintf('$this->%s', lcfirst($mockName));

            $this->addMock($definition, $data, $mockName);
        }
    }

    /**
     * @param string $originalTest
     * @param string $newTest
     * @param OutputInterface $output
     * @todo finish
     */
    private function displayPatch($originalTest, $newTest, OutputInterface $output)
    {
        $output->writeln('<info>Diff: Not implemented yet</info>');
    }

    /**
     * @param string $originalTest
     * @param string $newTest
     * @throws Exception
     * @return string
     */
    private function replaceHeaderOnly($originalTest, $newTest)
    {
        if (!preg_match('/^.*?}/s', $newTest, $matches)) {
            throw new Exception('No header found in new test');
        }

        $header = $matches[0];

        return preg_replace('/^.*?}/s', $header, $originalTest);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $serviceId
     * @param string $testFileName
     * @param string $template
     * @return string
     * @throws Exception
     */
    protected function handleExistingFile(
        InputInterface $input,
        OutputInterface $output,
        $serviceId,
        $testFileName,
        $template
    ) {
        if ($input->getOption('no-interaction')) {
            $output->writeln(sprintf("Test for '<info>%s</info>' already exist", $serviceId));
            return false;
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $choices = [
            'stop' => 'Stop',
            'replace' => 'Replace full test file',
            'diff' => 'Display the diff',
            'header' => 'full setup only',
        ];
        $question = new ChoiceQuestion(
            '<error>The test file already exist. What should i do now?</error>',
            $choices
        );

        $answer = $helper->ask($input, $output, $question);

        $originalTest = file_get_contents($testFileName);

        $answerId = array_flip($choices)[$answer];
        switch ($answerId) {
            case 'replace':
                break;
            case 'header':
                $template = $this->replaceHeaderOnly($originalTest, $template);
                break;
            case 'diff':
                $this->displayPatch($originalTest, $template, $output);
                return $template;
            case 'stop':
            default:
                return false;
        }
        return $template;
    }

}
