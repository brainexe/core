<?php

namespace BrainExe\Core\Logger;

use Monolog\Formatter\LineFormatter;

class Formatter extends LineFormatter
{

    /**
     * @param array $record
     * @return string
     */
    public function format(array $record)
    {
        unset($record['context']['channel']);

        return parent::format($record);
    }
}
