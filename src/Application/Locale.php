<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Core.Locale", public=false)
 */
class Locale
{

    /**
     * @todo add to app config
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
        putenv(sprintf('LANG=%s.UTF-8', $locale));
        setlocale(LC_MESSAGES, sprintf('%s.UTF-8', $locale));

        $domain = 'messages';
        bindtextdomain($domain, ROOT . '/lang/');
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }
}
