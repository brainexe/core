<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 */
class PasswordHasher
{
    const COST = 10;

    /**
     * @param string $password
     * @return string $hash
     */
    public function generateHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => self::COST
        ]);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verifyHash($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
