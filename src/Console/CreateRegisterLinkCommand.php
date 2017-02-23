<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\Authentication\RegisterTokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation
 */
class CreateRegisterLinkCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('user:register_link')
            ->setDescription('Create a register link');
    }

    /**
     * @var RegisterTokens
     */
    private $registerTokens;

    /**
     * @param RegisterTokens $registerTokens
     */
    public function __construct(RegisterTokens $registerTokens)
    {
        $this->registerTokens = $registerTokens;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = $this->registerTokens->addToken();

        $link = sprintf('/register/?token=%s', $token);

        $output->writeln(sprintf('<info>%s</info>', $link));
    }
}
