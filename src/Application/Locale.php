<?php

namespace BrainExe\Core\Application;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Translation\ServiceTranslationProvider;

/**
 * @Service
 */
class Locale implements ServiceTranslationProvider
{

    const DOMAIN   = 'messages';
    const LANG_DIR = ROOT . '/cache/lang/';

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @Inject({
     *     "%locales%",
     *     "%application.defaultLocale%"
     * });
     * @param string[] $locales
     * @param string $defaultLocale
     */
    public function __construct(array $locales, string $defaultLocale = '')
    {
        $this->locales = $locales;

        if ($defaultLocale) {
            $this->setLocale($defaultLocale);
        }
    }

    /**
     * @return string[]
     */
    public function getLocales() : array
    {
        return $this->locales;
    }

    /**
     * @param string $locale
     * @codeCoverageIgnore
     */
    public function setLocale(string $locale) : void
    {
        putenv(sprintf('LANG=%s.UTF-8', $locale));
        setlocale(LC_MESSAGES, sprintf('%s.UTF-8', $locale));

        bindtextdomain(self::DOMAIN, self::LANG_DIR);
        bind_textdomain_codeset(self::DOMAIN, 'UTF-8');
        textdomain(self::DOMAIN);
    }

    /**
     * @return string[]
     */
    public function getTokens()
    {
        return array_map(function (string $locale) {
            return 'locale.' . $locale;
        }, $this->locales);
    }
}
