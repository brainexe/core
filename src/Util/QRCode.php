<?php

namespace BrainExe\Core\Util;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 * @api
 */
class QRCode
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @Inject("%qr.baseUrl%")
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $data
     * @param int $size
     * @return string
     */
    public function generateQRLink($data, $size = 250)
    {
        return sprintf($this->baseUrl, $size, $size, urlencode($data));
    }
}
