<?php

namespace BrainExe\Core\Authentication\Command;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Authentication\Token as TokenModel;

/**
 * @CommandAnnotation("Authentication.Command.ListTokens")
 */
class ListTokens extends Command
{
    /**
     * @var TokenModel
     */
    private $token;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('token:list')
            ->setDescription('List user tokens')
            ->addArgument('user', InputArgument::REQUIRED);
    }

    /**
     * @param TokenModel $token
     */
    public function __construct(TokenModel $token)
    {
        parent::__construct();

        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = (int)$input->getArgument('user');
        $tokens = $this->token->getTokensForUser($userId);

        $table = new Table($output);
        $table->setHeaders(['token', 'roles']);

        foreach ($tokens as $token => $data) {
            $table->addRow([$token, implode(',', $data)]);
        }

        $table->render();
    }
}
