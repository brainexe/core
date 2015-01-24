<?php

namespace BrainExe\Core\Application;

/**
 * @service("Core.Locale", public=false)
 */
class Locale
{

    /**
     * @return string[]
     */
    public function getLocales()
    {
        return [
        'en_EN',
        'de_DE'
        ];
    }

    /**
     * @param string $locale
     * @codeCoverageIgnore
     */
    public function setLocale($locale)
    {
        putenv("LANG=$locale.UTF-8");
        setlocale(LC_MESSAGES, "$locale.UTF-8");

        $domain = 'messages';
        bindtextdomain($domain, ROOT . "/lang/");
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }
}