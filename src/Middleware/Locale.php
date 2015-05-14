<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Middleware;
use BrainExe\Core\Application\Locale as LocaleModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @Middleware("Middleware.Session", priority=null)
 */
class Locale extends AbstractMiddleware
{

    /**
     * @var LocaleModel
     */
    private $locale;

    /**
     * @Inject("@Core.Locale")
     * @param LocaleModel $locale
     */
    public function __construct(LocaleModel $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function processRequest(Request $request, Route $route)
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
