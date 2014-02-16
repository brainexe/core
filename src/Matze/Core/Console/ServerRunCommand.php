<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "console"}})
 */
class ServerRunCommand extends Command {
	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->addArgument('address', InputArgument::OPTIONAL, 'Address:port', 'localhost:8000')
			->setName('server:run')
			->setDescription('Runs PHP built-in web server');;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln(sprintf("Server running on <info>%s</info>\n", $input->getArgument('address')));

		$builder = new ProcessBuilder(array(PHP_BINARY, '-S', $input->getArgument('address')));
		$builder->setWorkingDirectory(ROOT . '/web/');
		$builder->setTimeout(null);
		$builder->getProcess()->run(function ($type, $buffer) use ($output) {
			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->write($buffer);
			}
		});
	}
}