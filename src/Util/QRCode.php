<?php

namespace BrainExe\Core\Util;

/**
 * @service(public=false)
 */
class QRCode
{

    /**
     * @param string $data
     * @param int $size
     * @return string
     */
    public function generatreQRLink($data, $size = 250)
    {
        $baseUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s';

        return sprintf($baseUrl, $size, $size, urlencode($data));
    }
}