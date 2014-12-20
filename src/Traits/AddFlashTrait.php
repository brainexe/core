<?php

namespace BrainExe\Core\Traits;

use Symfony\Component\HttpFoundation\Response;

trait AddFlashTrait
{

    /**
     * @param Response $response
     * @param string $type self::ALERT_*
     * @param string $text
     */
    protected function _addFlash(Response $response, $type, $text)
    {
        $response->headers->set('X-Flash', json_encode([$type, $text]));
    }
}
