<?php

namespace Matze\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig_Environment;

/**
 * @Command
 */
class CompileTemplatesCommand extends Command {

	/**
	 * @var string
	 */
	private $value_template_dir;

	/**
	 * @var Twig_Environment
	 */
	private $twig;

	/**
	 * @Inject({"%template.dir%", "@TwigCompiler"})
	 */
	public function __construct($value_template_dir, Twig_Environment $twig) {
		$this->value_template_dir = $value_template_dir;
		$this->twig = $twig;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('templates:compile')
			->setDescription('Compile all templates');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->write('Compile templates...');

		$finder = new Finder();
		$finder
			->files()
			->in($this->value_template_dir)
			->name("*twig");

		$start = microtime(true);
		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$filename = $file->getRelativePathname();

			$this->twig->loadTemplate($filename);

			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln(sprintf("Compiled %s", $filename));
			}
		}

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			$needed_time = microtime(true) - $start;
			$output->writeln(sprintf('Compiled %d templates in %0.1fms', count($finder), $needed_time * 1000));
		}

		$output->writeln('<info>done</info>');
	}

} 
