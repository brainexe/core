<?php

namespace BrainExe\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractCommand extends Command {

	/**
	 * @param OutputInterface $output
	 * @param Process $process
	 */
	protected function _checkProcess(OutputInterface $output, Process $process) {
		if (!$process->isSuccessful()) {
			$error = $process->getErrorOutput();
			$command = $process->getCommandLine();

			$output->writeln(sprintf('<error>Error in command: %s</error>', $command));
			$output->writeln(sprintf('<error>%s</error>', $error));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln(sprintf('<comment>%s</comment>...', $this->getDescription()));

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			$output->writeln('');
		}

		$start = microtime(true);

		$this->doExecute($input, $output);

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			$output->writeln(sprintf("<info>done in %0.1fms</info>", (microtime(true) - $start)*1000));
		} else {
			$output->writeln('<info>done</info>');
		}
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return mixed
	 */
	abstract protected function doExecute(InputInterface $input, OutputInterface $output);
} 