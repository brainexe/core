<?php

namespace BrainExe\Core\Translation;

interface TranslationProvider
{

    /**
     * @return string[]
     */
    public static function getTokens();
}
