<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Authentication\AnonymusUserVO;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Middleware("Middleware.Gentime")
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
        $username  = $this->getUsername($user);

        $diff = microtime(true) - $startTime;
        $this->info(
            sprintf(
                '%0.2fms - %s',
                $diff * 1000,
                $request->getRequestUri()
            ),
            [
                'channel'  => 'gentime',
                'time'     => round($diff * 1000, 2),
                'route'    => $request->attributes->get('_route'),
                'userName' => $username,
                'userId'   => $request->attributes->get('user_id')
            ]
        );
    }

    /**
     * @param UserVO|null $user
     * @return string
     */
    protected function getUsername($user)
    {
        if ($user instanceof UserVO && !$user instanceof AnonymusUserVO) {
            /** @var UserVO $user */
            return $user->getUsername();
        } else {
            return '-anonymous-';
        }
    }
}
