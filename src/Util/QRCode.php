<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 * @api
 */
class QRCode
{

    /**
     * @param string $data
     * @param int $size
     * @return string
     */
    public function generateQRLink($data, $size = 250)
    {
        // todo container.xml
        $baseUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s';

        return sprintf($baseUrl, $size, $size, urlencode($data));
    }
}
