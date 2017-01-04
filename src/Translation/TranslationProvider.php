<?php

namespace BrainExe\Core\Translation;

use Translation\Token;

interface TranslationProvider
{
    /**
     * @return string[]|Token[]
     */
    public static function getTokens();
}
