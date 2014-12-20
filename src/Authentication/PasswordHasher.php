<?php

namespace BrainExe\Core\Authentication;

/**
 * @service(public=false)
 */
class PasswordHasher
{

    /**
     * @param string $password
     * @return string $hash
     */
    public function generateHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [
        'cost' => 7
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
