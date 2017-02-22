<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use RuntimeException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\ConsoleEvents;

/**
 * @EventListener
 */
class SystemUserListener
{
    /**
     * @var string
     */
    private $expectedUser;

    /**
     * @Inject({
     *   "user" = "%server.user%"
     * })
     * SystemUserListener constructor.
     * @param string $user
     */
    public function __construct(string $user)
    {
        $this->expectedUser = $user;
    }

    /**
     * @Listen(ConsoleEvents::COMMAND)
     * @throws RuntimeException
     */
    public function handleCommand(ConsoleCommandEvent $event)
    {
        $currentUser = $this->getCurrentUser();

        if ($this->expectedUser !== $currentUser) {
            throw new RuntimeException(
                sprintf(
                    'Not supported user: %s (allowed: %s)',
                    $currentUser,
                    $this->expectedUser
                )
            );
        }
    }

    private function getCurrentUser() : string
    {
        return posix_getpwuid(posix_geteuid())['name'];
    }

}
