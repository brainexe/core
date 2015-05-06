<?php

namespace BrainExe\Core\Authentication\Settings;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @api
 * @Service("User.Settings", public=true)
 */
class Settings
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@User.Settings.Gateway")
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
        $this->gateway->set($userId, $setting, $value);
    }
}
