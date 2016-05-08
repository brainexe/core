<?php

namespace BrainExe\Core\Application;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Core.Locale", public=true)
 */
class Locale
{

    const DOMAIN = 'messages';

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @Inject({"%locales%", "%application.defaultLocale%"});
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
    public function setLocale(string $locale)
    {
        putenv(sprintf('LANG=%s.UTF-8', $locale));
        setlocale(LC_MESSAGES, sprintf('%s.UTF-8', $locale));

        bindtextdomain(self::DOMAIN, ROOT . '/lang/');
        bind_textdomain_codeset(self::DOMAIN, 'UTF-8');
        textdomain(self::DOMAIN);
    }
}
