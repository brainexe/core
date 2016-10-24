<?php

namespace BrainExe\Core\Translation;

trait TranslationTrait
{
    /**
     * @param string $string
     * @param array ...$params
     * @return string
     */
    public function translate(string $string, ...$params) : string
    {
        return vsprintf(gettext($string), $params);
    }
}
