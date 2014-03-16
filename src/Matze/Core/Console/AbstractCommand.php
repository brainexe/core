<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractCommand extends Command {

	/**
	 * @param OutputInterface $output
	 * @param Process $process
	 */
	protected function _chckProcess(OutputInterface $output, Process $process) {
		if (!$process->isSuccessful()) {
			$error = $process->getErrorOutput();
			$command = $process->getCommandLine();

			$output->writeln(sprintf('<error>Error in command: %s</error>', $command));
			$output->writeln(sprintf('<error>%s</error>', $error));
		}
	}

} 