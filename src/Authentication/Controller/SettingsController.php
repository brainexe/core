<?php

namespace BrainExe\Core\Authentication\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Settings\Settings;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class SettingsController
{
    /**
     * @var Settings
     */
    private $settings;

    /**
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
     * @Guest
     */
    public function all(Request $request)
    {
        $userId = $request->attributes->getInt('user_id');

        return $this->settings->getAll($userId);
    }

    /**
     * @param Request $request
     * @param string $key
     * @return bool
     * @Route("/settings/{key}/", name="settings.set", methods="POST")
     */
    public function set(Request $request, string $key) : bool
    {
        $userId = $request->attributes->getInt('user_id');
        $value  = $request->request->get('value');

        $this->settings->set($userId, $key, $value);

        return true;
    }
}
