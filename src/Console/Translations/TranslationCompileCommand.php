<?php

namespace BrainExe\Core\Console\Translations;

use BrainExe\Core\Console\AbstractCommand;
use BrainExe\Core\Util\FileSystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Command
 */
class TranslationCompileCommand extends AbstractCommand
{
    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('translation:compile')
        ->setDescription('Compile PO files');
    }

    /**
     * @inject({"@Finder", "@ProcessBuilder", "@FileSystem"})
     * @param Finder $finder
     * @param ProcessBuilder $processBuilder
     * @param FileSystem $fileSystem
     */
    public function __construct(Finder $finder, ProcessBuilder $processBuilder, FileSystem $fileSystem)
    {
        $this->processBuilder = $processBuilder;
        $this->finder         = $finder;
        $this->fileSystem     = $fileSystem;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $langPath = ROOT . 'lang/';

        if (!$this->fileSystem->exists($langPath)) {
            $output->writeln(sprintf('<error>Lang directory does not exist: %s</error>', $langPath));
            return;
        }

        $files = $this->finder
            ->directories()
            ->in($langPath)
            ->depth(0);

        $command = 'msgfmt %smessages.po -o %smessages.mo';

        foreach ($files as $dir) {
            /** @var SplFileInfo $dir */
            $locale = $dir->getRelativePathname();
            $localePath = sprintf('%s%s/LC_MESSAGES/', $langPath, $locale);

            $process = $this->processBuilder
                ->setArguments([sprintf($command, $localePath, $localePath)])
                ->getProcess();

            $process->mustRun();

            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln(sprintf("Compiled %s", $locale));
            }
        }
    }
}
