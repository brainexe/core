<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Core;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @Command
 */
class TestCreateAllCommand extends Command {

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
			->setName('test:create:all')
			->addArgument('root', InputArgument::OPTIONAL, 'source root directory (without src)', ROOT);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_initContainerBuilder();

		$ids = $this->_container_builder->getServiceIds();

		foreach ($ids as $service_id) {
			try {
				$this->_handleService($input, $output, $service_id);
			} catch (InvalidArgumentException $e) {
				$output->writeln(sprintf('<error>%s: %s</error>', $service_id, $e->getMessage()));
			}
		}
	}

	/**
	 * @param string $service_namespace
	 * @return string
	 */
	private function _getTestFileName($service_namespace) {
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $service_namespace);

		return sprintf('%sTests/%sTest.php', ROOT,  $path);
	}


	/**
	 * @param string $service_id
	 * @return object
	 */
	private function _getService($service_id) {
		return $this->_container_builder->get($service_id);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param $service_id
	 * @throws \Exception
	 */
	protected function _handleService(InputInterface $input, OutputInterface $output, $service_id) {
		$service_object     = $this->_getService($service_id);

		$service_reflection = new ReflectionClass($service_object);
		$service_namespace  = $service_reflection->getName();

		$src = $input->getArgument('root') . 'src/';
		if (strpos($service_reflection->getFileName(), $src) !== 0) {
			return;
		}

		$test_file_name = $this->_getTestFileName($service_namespace);

		if (!file_exists($test_file_name)) {
			$output->writeln("create: <info>$service_id</info> - <info>" . $service_reflection->getFileName()."<info>");

			$input = new ArrayInput(['command' => 'test:create', 'service' => $service_id]);
			$this->getApplication()->run($input, $output);

		}
	}

	private function _initContainerBuilder() {
		$this->_container_builder = Core::rebuildDIC(false);
	}

}
