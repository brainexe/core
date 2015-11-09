<?php

namespace BrainExe\Core\Redis\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @CommandAnnotation("Redis.Migration.Export")
 * @codeCoverageIgnore
 */
class Command extends SymfonyCommand
{
    use RedisTrait;

    const REDIS_KEY = 'migrations';

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @Inject({"@Finder", "@ProcessBuilder"})
     * @param Finder $finder
     * @param ProcessBuilder $processBuilder
     */
    public function __construct(Finder $finder, ProcessBuilder $processBuilder)
    {
        $this->finder = $finder;
        $this->processBuilder = $processBuilder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('redis:migrate')
            ->setDescription('Apply all pending migrations')
            ->addArgument('directory', InputArgument::OPTIONAL)
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'Dry run only');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = new Finder();
        $files
            ->in($input->getArgument('directory'))
            ->name('*.php');

        $appliedMigrations = $this->getRedis()->hgetall(self::REDIS_KEY);
        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $basename = $file->getBasename('.php');
            if (!isset($appliedMigrations[$basename])) {
                $this->apply($file->getFilename(), $output);
            }
        }
    }

    /**
     * @param string $fileName
     * @param OutputInterface $output
     * @throws Exception
     */
    private function apply($fileName, OutputInterface $output)
    {
        include_once $fileName;

        $basename  = basename($fileName, '.php');
        $className = $this->getClassName($basename);

        if (!class_exists($className)) {
            throw new Exception(sprintf('Class %s not found in file', $className, $fileName));
        }
        /** @var MigrationInterface $file */
        $file = new $className();
        $file->execute($this->getRedis(), $output);

        $this->getRedis()->hset(self::REDIS_KEY, $basename, 1);
    }

    /**
     * @param string $file
     * @return string
     */
    private function getClassName($file)
    {
        return $file; // todo
    }
}
