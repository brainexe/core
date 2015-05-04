<?php

namespace BrainExe\Core\Authentication;

use JsonSerializable;

/**
 * @api
 */
class UserVO implements JsonSerializable
{

    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password_hash;

    /**
     * @var string
     */
    public $one_time_secret;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string[]
     */
    public $roles = [];

    /**
     * @var string
     */
    public $avatar;

    /**
     * @param string $role
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password_hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'avatar'   => $this->avatar,
            'roles'    => $this->roles,
        ];
    }
}
