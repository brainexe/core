<?php

namespace BrainExe\Core\Authentication\UserSettings;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("UserSettings", public=false)
 */
class UserSettings
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@UserSettings.Gateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param int $userId
     * @param string $setting
     * @return string
     */
    public function get($userId, $setting)
    {
        return $this->gateway->get($userId, $setting);
    }

    /**
     * @param int $userId
     * @return string[]
     */
    public function getAll($userId)
    {
        return $this->gateway->getAll($userId);
    }

    /**
     * @param int $userId
     * @param string $setting
     * @param string $value
     */
    public function set($userId, $setting, $value)
    {
        $this->gateway->get($userId, $setting, $value);
    }
}
