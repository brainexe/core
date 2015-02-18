<?php

namespace BrainExe\Core\Console\Translations;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Command;
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
class TranslationFindCommand extends AbstractCommand
{

    /**
     * @Inject({"@Finder", "@ProcessBuilder", "@FileSystem"})
     * @param Finder $finder
     * @param ProcessBuilder $processBuilder
     * @param FileSystem $fileSystem
     */
    public function __construct(Finder $finder, ProcessBuilder $processBuilder, FileSystem $fileSystem)
    {
        $this->processBuilder = $processBuilder;
        $this->finder = $finder;
        $this->fileSystem = $fileSystem;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('translation:find')
            ->setDescription('Finds all marked translation');
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $command = 'find %s -type f -iname "*.php" | xgettext --keyword=__ --keyword=t -j -f - -o %slang/messages.pot';

        $process = $this->processBuilder
            ->setArguments([sprintf($command, ROOT, ROOT)])
            ->getProcess();

        $process->run();
        $this->checkProcess($output, $process);

        $langPath = ROOT . 'lang/';

        $dirs = $this->finder
            ->directories()
            ->in($langPath)
            ->depth(0);

        $command = 'msgmerge -vU %smessages.po %smessages.pot';

        foreach ($dirs as $dir) {
            /** @var SplFileInfo $dir */
            $locale = $dir->getRelativePathname();
            $localePath = sprintf('%s%s/LC_MESSAGES/', $langPath, $locale);

            $process = $this->processBuilder
                ->setArguments([sprintf($command, $localePath, $langPath)])
                ->getProcess();

            $process->run();

            $this->checkProcess($output, $process);

            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln(sprintf("Process %s", $locale));
            }
        }
    }
}
