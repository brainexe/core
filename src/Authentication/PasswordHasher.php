<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;

/**
 * @Service("Core.Authentication.PasswordHasher")
 */
class PasswordHasher
{
    /**
     * @var int
     */
    private $cost;

    /**
     * @Inject("%application.passwordHasher.cost%")
     * @param int $cost
     */
    public function __construct(int $cost)
    {
        $this->cost = $cost;
    }

    /**
     * @param string $password
     * @return string $hash
     */
    public function generateHash(string $password) : string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $this->cost
        ]);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyHash(string $password, string $hash) : bool
    {
        return password_verify($password, $hash);
    }
}
