<?php

namespace BrainExe\Core\Util;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Application\UserException;

/**
 * Parse user input into an unix timestamp
 * @Service(shared=false)
 * @api
 */
class TimeParser
{

    /**
     * @var int[]
     */
    private const TIME_MODIFIERS = [
        's' => 1,
        'm' => 60,
        'h' => 3600,
        'd' => 86400,
        'w' => 604800,
        'y' => 31536000,
    ];

    /**
     * @param string $string
     * @throws UserException
     * @return int
     */
    public function parseString(string $string)
    {
        $now = time();

        if (empty($string)) {
            return 0;
        } elseif (is_numeric($string)) {
            $timestamp = $now + (int)$string;
        } elseif (preg_match('/^(\d+)\s*(\w)$/', trim($string), $matches)) {
            $timestamp = $this->withModifier($matches, $now);
        } else {
            $timestamp = strtotime($string);
        }

        if ($timestamp < $now) {
            throw new UserException(sprintf('Time %s is invalid', $string));
        }

        return $timestamp;
    }

    /**
     * @param array $matches
     * @param int $now
     * @return int
     * @throws UserException
     */
    private function withModifier(array $matches, int $now) : int
    {
        $modifier = strtolower($matches[2]);
        if (empty(self::TIME_MODIFIERS[$modifier])) {
            throw new UserException(sprintf('Invalid time modifier %s', $modifier));
        }

        return $now + (int)$matches[1] * self::TIME_MODIFIERS[$modifier];
    }
}
