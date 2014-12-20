<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=1)
 */
class GentimeMiddleware extends AbstractMiddleware
{

    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        $startTime = $request->server->get('REQUEST_TIME_FLOAT');
        $diff      = microtime(true) - $startTime;
        $user      = $request->attributes->get('user');

        if ($user) {
            /** @var UserVO $user */
            $username = $user->getUsername();
        } else {
            $username = '-anonymous-';
        }

        $this->info(
            sprintf(
                '%0.2fms (route: %s, user:%s)',
                $diff * 1000,
                $request->getRequestUri(),
                $username
            ),
            ['channel' => 'gentime']
        );
    }
}
