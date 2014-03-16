<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Command
 */
class TranslationFindCommand extends AbstractCommand {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('translation:find')
			->setDescription('Finds all marked translation');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->write('Find Translations...');

		$command = 'find %s -type f -iname "*.php" | xgettext --keyword=__ --keyword=t -j -f - -o %slang/messages.pot';

		$process = new Process(sprintf($command, ROOT, ROOT));
		$process->run();
		$this->_chckProcess($output, $process);

		$output->writeln('<error>' . $process->getErrorOutput() . '</error>');

		$lang_path = ROOT . '/lang/';

		$finder = new Finder();
		$finder
			->directories()
			->in($lang_path)
			->depth(0);

		$command = 'msgmerge -vU %smessages.po %smessages.pot';

		foreach ($finder as $dir) {
			/** @var SplFileInfo $dir */
			$locale = $dir->getRelativePathname();
			$locale_path = sprintf('%s%s/LC_MESSAGES/', $lang_path, $locale);

			$process = new Process(sprintf($command, $locale_path, $lang_path));
			$process->run();
			$this->_chckProcess($output, $process);

			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln(sprintf("Process %s", $locale));
			}
		}

		$output->writeln('<info>done</info>');
	}
}