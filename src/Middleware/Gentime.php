<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware("Middleware.Gentime", priority=1)
 */
class Gentime extends AbstractMiddleware
{

    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function processResponse(Request $request, Response $response)
    {
        $startTime = $request->server->get('REQUEST_TIME_FLOAT');
        $user      = $request->attributes->get('user');

        if ($user) {
            /** @var UserVO $user */
            $username = $user->getUsername();
        } else {
            $username = '-anonymous-';
        }

        $diff = microtime(true) - $startTime;
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
