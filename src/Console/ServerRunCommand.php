<?php

namespace BrainExe\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Command
 */
class ServerRunCommand extends Command {

	/**
	 * @var string
	 */
	private $_value_address;

	/**
	 * @Inject("%server.address%")
	 * @param string $value_address
	 */
	public function __construct($value_address) {
		$this->_value_address = $value_address;
		parent::__construct(null);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->addArgument('address', InputArgument::OPTIONAL, 'Address:port')
			->addOption('quiet', 'q', InputOption::VALUE_NONE)
			->setName('server:run')
			->setDescription('Runs PHP built-in web server');;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$address = $input->getArgument('address') ?: $this->_value_address;

		$output->writeln(sprintf("Server running on <info>%s</info>\n", $address));

		$builder = new ProcessBuilder(array(PHP_BINARY, '-S', $address));
		$builder->setWorkingDirectory(ROOT . '/web/');
		$builder->setTimeout(null);
		$builder->getProcess()->run(function ($type, $buffer) use ($output, $input) {
			unset($type);
			if (!$input->getOption('quiet')) {
				$output->write($buffer);
			}
		});
	}
}