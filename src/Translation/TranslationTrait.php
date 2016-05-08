<?php

namespace BrainExe\Core\Translation;

trait TranslationTrait
{
    /**
     * @param string $string
     * @param array ...$params
     * @return string
     */
    protected function translate(string $string, ...$params) : string
    {
        return vsprintf(gettext($string), $params);
    }
}
