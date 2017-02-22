<?php

namespace BrainExe\Core\Authentication\Command;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Authentication\Token as TokenModel;

/**
 * @CommandAnnotation("Authentication.Command.CreateToken")
 */
class CreateToken extends Command
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
        $this->setName('token:create')
            ->setDescription('Create user token')
            ->addArgument('user', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::OPTIONAL);
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
        $roles  = (string)$input->getArgument('roles');

        $token = $this->token->addToken($userId, explode(',', $roles));
        $output->writeln(
            sprintf(
                'Created token %s for user %d with roles: %s',
                $token,
                $userId,
                $roles
            )
        );
    }
}
