<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\DependencyInjection\Rebuild;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\FileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("ClearCacheCommand")
 */
class ClearCacheCommand extends Command
{

    use EventDispatcherTrait;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var FileSystem
     */
    private $filesystem;

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clears the local cache')
            ->setAliases(['cc']);
    }

    /**
     * @Inject({"@Finder", "@FileSystem", "@Core.Rebuild"})
     * @param Finder $finder
     * @param FileSystem $filesystem
     * @param Rebuild $rebuild
     */
    public function __construct(Finder $finder, FileSystem $filesystem, Rebuild $rebuild)
    {
        $this->finder = $finder;
        $this->filesystem = $filesystem;
        $this->rebuild = $rebuild;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Clear Cache...');

        $files = $this->finder
            ->files()
            ->in(ROOT . 'cache')
            ->name('*.php')
            ->notName('assets.php');

        $this->filesystem->remove($files);

        $output->writeln('<info>done</info>');

        $output->write('Rebuild DIC...');
        $this->rebuild->rebuildDIC(true);
        $output->writeln('<info>done</info>');

        $event = new ClearCacheEvent($this->getApplication(), $input, $output);
        $this->dispatchEvent($event);
    }
}
