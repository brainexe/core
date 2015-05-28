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
        $value = $this->gateway->get($userId, $setting);

        if (empty($value) && $userId) {
            $value = $this->get(0, $setting);
        }

        return $value;
    }

    /**
     * @param int $userId
     * @return string[]
     */
    public function getAll($userId)
    {
        $values = $this->gateway->getAll($userId);

        if ($userId) {
            $values = array_merge($this->getAll(0), $values);
        }

        return $values;
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
