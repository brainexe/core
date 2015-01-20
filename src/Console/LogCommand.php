<?php

namespace BrainExe\Core\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * @Command
 * @codeCoverageIgnore
 */
class LogCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('debug:log')
            ->setDescription('tail -f on log files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in(ROOT . 'logs')
            ->name("*.log");

        $colors = [
        'cyan',
        'magenta',
        'red',
        'yellow',
        'black',
        'blue',
        'green',
        'white'
        ];

        $index = 0;
        foreach ($finder as $file) {
            $color = $colors[$index++ % (count($colors) - 1)];

            /** @var SplFileInfo $file */
            $filename = $file->getFilename();

            $output->writeln(sprintf("<fg=%s>%s</>", $color, $filename));

            $process = new Process(sprintf('tail -fn0 %s', $file->getPathname()));
            $process->setTimeout(0);
            $process->setIdleTimeout(0);

            $process->start(function($type, $buffer) use ($output, $filename, $color) {
                unset($type);
                $output->write(sprintf("<fg=yellow>%s: %s</>", $filename, $buffer));
            });
        }
        sleep(100);
    }
}
