<?php

namespace BrainExe\Core\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig_Environment;

/**
 * @Command
 */
class CompileTemplatesCommand extends AbstractCommand {

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
	 * @param string $value_template_dir
	 * @param Twig_Environment $twig
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
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		if (CORE_STANDALONE) {
			return;
		}

		$finder = new Finder();
		$finder
			->files()
			->in($this->value_template_dir)
			->name("*twig");

		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$filename = $file->getRelativePathname();

			$this->twig->loadTemplate($filename);

			if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
				$output->writeln(sprintf("Compiled %s", $filename));
			}
		}

		if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
			$output->writeln(sprintf('Compiled %d templates', count($finder)));
		}
	}

} 
