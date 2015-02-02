<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=null)
 */
class LocaleMiddleware extends AbstractMiddleware
{

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @inject("@Core.Locale")
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route, $routeName)
    {
        $session = $request->getSession();

        if ($request->query->has('locale')) {
            $availableLocales = $this->locale->getLocales();

            $locale = $request->query->get('locale');
            if (!in_array($locale, $availableLocales)) {
                // invalid locale -> use first defined locale as fallback
                $locale = $availableLocales[0];
            }

            $session->set('locale', $locale);
        } else {
            $locale = $session->get('locale');
        }

        if ($locale) {
            $this->locale->setLocale($locale);
        }
    }
}
