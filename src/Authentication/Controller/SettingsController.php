<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Settings\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("Authentication.Controller.Settings")
 */
class SettingsController
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @Inject("@User.Settings")
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @return string[]
     * @Route("/settings/", name="settings.all", methods="GET")
     */
    public function all(Request $request)
    {
        return $this->settings->getAll($request->attributes->get('user_id'));
    }

    /**
     * @param Request $request
     * @param string $key
     * @param string $value
     * @return bool
     * @Route("/settings/{key}/{value}/", name="settings.set", methods="POST")
     */
    public function set(Request $request, $key, $value)
    {
        $userId = $request->attributes->get('user_id');

        $this->settings->set($userId, $key, $value);

        return true;
    }
}
