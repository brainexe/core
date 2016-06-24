<?php

namespace BrainExe\Core\Translation;

interface ServiceTranslationProvider
{
    /**
     * @return string[]
     */
    public function getTokens();
}
