<?php

namespace Ig\StratCity\Classes\System\Commands\Test;

use BrainExe\Core\Core;
use BrainExe\Core\Traits\ConfigTrait;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TestData {
	public $setter_calls = [];
	public $default_tests = [];
	public $mock_properties = [];
	public $use_statements = [];
	public $local_mocks = [];
	public $constructor_arguments = [];
}

/**
 * @Command
 */
class TestCreateCommand extends Command {

	use ConfigTrait;

	/**
	 * Cached container builder
	 * @var ContainerBuilder|null
	 */
	private $_container_builder = null;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('test:create')
			->addArgument('service', InputArgument::REQUIRED, 'service id, e.g. IndexController')
			->addOption('dry', 'd', InputOption::VALUE_NONE, 'only display the generated test');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_initContainerBuilder();

		$service_id = $input->getArgument('service');

		$service_object = $this->_getService($service_id);
		$service_definition = $this->_getDefinition($service_id);

		$service_reflection = new ReflectionClass($service_object);
		$service_namespace = $service_reflection->getName();
		$service_class_name = $this->_getShortClassName($service_namespace);

		$test_data = new TestData();

		$test_data->use_statements[] = PHPUnit_Framework_TestCase::class;
		$test_data->use_statements[] = PHPUnit_Framework_MockObject_MockObject::class;
		$test_data->use_statements[] = $service_namespace;

		$blacklisted_methods = $this->_getBlacklistedMethods($service_reflection);

		$methods = $service_reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			$method_name = $method->getName();

			if (in_array($method_name, $blacklisted_methods)) {
				continue;
			}

			if ($method->getDeclaringClass() == $service_reflection) {
				$test_data->default_tests[] = $this->_getDummyTestCode($test_data, $method);
			}
		}

		$this->_setupConstructor($service_definition, $test_data);

		foreach ($service_definition->getMethodCalls() as $method_call) {
			list ($setter_name, $references) = $method_call;
			/** @var Reference $reference */
			foreach ($references as $reference) {
				$reference_service_id = (string)$reference;

				if ('%' === substr($reference_service_id, 0, 1)) {
					// add config setter with current config value
					$parameter_name  = substr($reference, 1, -1);
					$parameter_value = $this->getParameter($parameter_name);

					$formatted_parameter = var_export($parameter_value, true);

					$test_data->setter_calls[] = sprintf("\t\t\$this->_subject->%s(%s);", $setter_name, $formatted_parameter);

				} else {
					// add setter for model mock
					$reference_service = $this->_getDefinition($reference_service_id);
					$mock_name         = $this->_getShortClassName($reference_service->getClass());

					$test_data->setter_calls[]    = sprintf("\t\t\$this->_subject->%s(\$this->_mock%s);", $setter_name,
															$mock_name);
					$this->_addMock($reference_service, $test_data, $mock_name);
				}
			}
		}

		$test_data->use_statements = array_map(function($class_name) {
			return sprintf('use %s;', $class_name);
		}, array_unique($test_data->use_statements));

		$test_template = file_get_contents(CORE_ROOT . '/../scripts/phpunit_template.php.tpl');
		$test_template = str_replace('%test_namespace%', $this->_getTestNamespace($service_namespace, $service_class_name), $test_template);
		$test_template = str_replace('%service_namespace%', $service_namespace, $test_template);
		$test_template = str_replace('%class_name%', $service_class_name, $test_template);
		$test_template = str_replace('%setters%', implode("\n", $test_data->setter_calls), $test_template);
		$test_template = str_replace('%default_tests%', implode("\n", $test_data->default_tests), $test_template);
		$test_template = str_replace('%mock_properties%', implode("\n", $test_data->mock_properties), $test_template);
		$test_template = str_replace('%use_statements%', implode("\n", $test_data->use_statements), $test_template);
		$test_template = str_replace('%local_mocks%', implode("\n", $test_data->local_mocks), $test_template);
		$test_template = str_replace('%constructor_arguments%', implode(", ", $test_data->constructor_arguments), $test_template);

		$test_file_name = $this->_getTestFileName($input, $service_namespace);

		if ($input->getOption('dry')) {
			$output->writeln($test_template);
			return;
		}

		// handle already existing test file
		if (file_exists($test_file_name)) {
			if ($input->getOption('no-interaction')) {
				$output->writeln(sprintf("Test for '<info>%s</info>' already exist", $service_id));
				return;
			}
			
			/** @var DialogHelper $dialog */
			$dialog = $this->getHelper('dialog');


			if (!$dialog->askConfirmation($output, '<error>The test file already exist. Do you want to override it?</error> (y/n)')) {
				return;
			}
		}

		$test_dir = dirname($test_file_name);

		if (!is_dir($test_dir)) {
			mkdir($test_dir, 0777, true);
		}

		file_put_contents($test_file_name, $test_template);

		$output->writeln(sprintf("Created Test for '<info>%s</info>' in <info>%s</info>", $service_id, $test_file_name));
	}

	/**
	 * @param InputInterface $input
	 * @param string $service_namespace
	 * @return string
	 */
	private function _getTestFileName(InputInterface $input, $service_namespace) {
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $service_namespace);

		return sprintf('%sTests/%sTest.php', ROOT,  $path);
	}

	/**
	 * @param string $service_id
	 * @return Definition
	 */
	private function _getDefinition($service_id) {
		return $this->_container_builder->getDefinition($service_id);
	}

	/**
	 * @param string $service_id
	 * @return object
	 */
	private function _getService($service_id) {
		return $this->_container_builder->get($service_id);
	}

	/**
	 * @param string $service_namespace
	 * @param string $service_class_name
	 * @return string
	 */
	private function _getTestNamespace($service_namespace, $service_class_name) {
		return "Tests\\" . $service_namespace;
	}

	/**
	 * Generated dummy test code for a given service method
	 *
	 * @param TestData $test_data
	 * @param ReflectionMethod $method
	 * @return string
	 */
	private function _getDummyTestCode(TestData $test_data, ReflectionMethod $method) {
		$method_name = $method->getName();

		$parameter_list = [];
		$variable_list = [];

		foreach ($method->getParameters() as $parameter) {
			$parameter_list[] = $variable_name = sprintf('$' . $parameter->getName());

			$value = 'null';
			if ($parameter->isOptional()) {
				$value = $parameter->getDefaultValue();
			} if ($parameter->getClass()) {
				$class = $parameter->getClass()->name;
				$test_data->use_statements[] = $class;
				$value = sprintf('new %s()', $this->_getShortClassName($class));
			}

			$variable_list[] = sprintf("\t\t%s = %s;", $variable_name, $value);
		}

		$test_code = sprintf("\tpublic function test%s() {\n", ucfirst($method_name));
		$test_code .= "\t\t\$this->markTestIncomplete('This is only a dummy implementation');\n\n";

		$test_code .= implode("\n", $variable_list) . "\n";
		$parameter_string = implode(', ', $parameter_list);

		$has_return_value = strpos($method->getDocComment(), '@return') !== false;
		if ($has_return_value) {
			$test_code .= sprintf("\t\t\$actual_result = \$this->_subject->%s(%s);\n", $method_name, $parameter_string);
		} else {
			$test_code .= sprintf("\t\t\$this->_subject->%s(%s);\n", $method_name, $parameter_string);
		}

		$test_code .= "\t}\n";

		return $test_code;
	}

	private function _initContainerBuilder() {
		if ($this->_container_builder !== null) {
			return;
		}

		$this->_container_builder = Core::rebuildDIC(false);
	}

	/**
	 * @param string
	 * @return string
	 */
	private function _getShortClassName($full_class_name) {
		// Strip off namespace
		$last_backslash_pos = strrpos($full_class_name, '\\');

		if (!$last_backslash_pos) {
			return $full_class_name;
		}

		return substr($full_class_name, $last_backslash_pos + 1);
	}

	/**
	 * @param ReflectionClass $service_reflection
	 * @return string[]
	 */
	protected function _getBlacklistedMethods(ReflectionClass $service_reflection) {
		$blacklisted_methods = [];

		foreach ($service_reflection->getTraitNames() as $trait) {
			$reflection = new ReflectionClass($trait);
			foreach ($reflection->getMethods() as $method) {
				$blacklisted_methods[] = $method->getName();
			}
		}

		$blacklisted_methods[] = '__construct';
		return $blacklisted_methods;
	}

	/**
	 * @param $reference_service
	 * @param $test_data
	 * @param $mock_name
	 */
	protected function _addMock(Definition $reference_service, TestData $test_data, $mock_name) {
		$test_data->use_statements[]  = $reference_service->getClass();
		$test_data->local_mocks[]     = sprintf("\t\t\$this->_mock%s = \$this->getMock(%s::class, [], [], '', false);", $mock_name,	$mock_name);
		$test_data->mock_properties[] = sprintf("\t/**\n\t * @var %s|PHPUnit_Framework_MockObject_MockObject\n\t */\n\tprivate \$_mock%s;\n", $mock_name, $mock_name);
	}

	/**
	 * @param Definition $service_definition
	 * @param TestData $test_data
	 */
	protected function _setupConstructor(Definition $service_definition, TestData $test_data) {
		foreach ($service_definition->getArguments() as $reference) {
			if ($reference instanceof Definition) {
				$definition = $reference;
				$mock_name  = $this->_getShortClassName($definition->getClass());
			} else {
				// add setter for model mock
				$definition = $this->_getDefinition((string)$reference);
				$mock_name  = $this->_getShortClassName($definition->getClass());
			}

			$test_data->constructor_arguments[] = sprintf('$this->_mock%s', $mock_name);

			$this->_addMock($definition, $test_data, $mock_name);
		}
	}
}
