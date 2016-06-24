<?php

namespace BrainExe\Core\Authentication;

use JsonSerializable;

/**
 * @api
 */
class UserVO implements JsonSerializable
{

    const PROPERTY_AVATAR = 'avatar';
    const PROPERTY_EMAIL  = 'email';

    const ROLE_ADMIN = 'admin';
    const ROLE_USER  = 'user';

    const AVATAR_1 = 'avatar1.png';
    const AVATAR_2 = 'avatar2.png';
    const AVATAR_3 = 'avatar3.png';
    const AVATAR_4 = 'avatar4.png';
    const AVATAR_5 = 'avatar5.png';

    const DEFAULT_AVATAR = self::AVATAR_5;

    const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_USER
    ];

    const AVATARS = [
        self::AVATAR_1,
        self::AVATAR_2,
        self::AVATAR_3,
        self::AVATAR_4,
        self::AVATAR_5,
    ];

    /**
     * @var int
     */
    public $id = 0;

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
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role) : bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @return string[]
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword() :string
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
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'userId'   => $this->id,
            'username' => $this->username,
            'roles'    => $this->roles,
            'email'    => $this->email,
            'avatar'   => $this->avatar,
        ];
    }
}
