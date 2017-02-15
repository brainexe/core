<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @todo matze
 * CommandAnnotation
 */
class ServerRunCommand extends Command
{

    /**
     * @var string
     */
    private $serverAddress;

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @Inject({
     *     "processBuilder" = "@ProcessBuilder",
     *     "address" = "%server.host%"
     * })
     * @param ProcessBuilder $processBuilder
     * @param string $address
     */
    public function __construct(ProcessBuilder $processBuilder, string $address)
    {
        $this->serverAddress  = $address;
        $this->processBuilder = $processBuilder;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('address', InputArgument::OPTIONAL, 'host:port')
            ->addOption('quiet', 'q', InputOption::VALUE_NONE)
            ->setName('server:run')
            ->setDescription('Runs PHP built-in web server');
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $address = $input->getArgument('address') ?: $this->serverAddress;

        $output->writeln(sprintf("Server running on <info>%s</info>\n", $address));

        $process = $this->processBuilder
            ->setArguments([PHP_BINARY, '-S', $address])
            ->setWorkingDirectory(ROOT . 'web/')
            ->setTimeout(null)
            ->getProcess();

        $process->run(function ($type, $buffer) use ($output, $input) {
            unset($type);
            if (!$input->getOption('quiet')) {
                $output->write($buffer);
            }
        });
    }
}
