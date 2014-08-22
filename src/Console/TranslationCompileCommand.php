<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * @Command
 */
class TranslationCompileCommand extends AbstractCommand {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('translation:compile')
			->setDescription('Compile PO files');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$lang_path = ROOT . '/lang/';

		$finder = new Finder();
		$finder
			->directories()
			->in($lang_path)
			->depth(0);

		$command = 'msgfmt %smessages.po -o %smessages.mo';

		foreach ($finder as $dir) {
			/** @var SplFileInfo $dir */
			$locale = $dir->getRelativePathname();
			$locale_path = sprintf('%s%s/LC_MESSAGES/', $lang_path, $locale);

			$process = new Process(sprintf($command, $locale_path, $locale_path));
			$process->run();
			$this->_checkProcess($output, $process);

			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln(sprintf("Compiled %s", $locale));
			}
		}
	}
}