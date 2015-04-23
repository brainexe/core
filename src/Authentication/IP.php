<?php

namespace BrainExe\Core\Authentication;

use BrainExe\Annotations\Annotations\Service;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Service("Authentication.IP", public=false)
 */
class IP
{

    /**
     * @param Request $request
     * @return bool
     */
    public function isLocalRequest(Request $request)
    {
        $requestIp = $request->server->get('REMOTE_ADDR');

        $allowedIps = [
            '127.0.0.1'
        ];

        if (in_array($requestIp, $allowedIps)) {
            return true;
        }

        return !filter_var($requestIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
    }
}
