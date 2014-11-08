<?php

namespace BrainExe\Core\Middleware;

use BrainExe\Core\Core;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=null)
 */
class LocaleMiddleware extends AbstractMiddleware {

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route, $route_name) {
		$session = $request->getSession();

		// TODO move into DIC
		$available_locales = ['en_EN', 'de_DE'];

		if ($request->query->has('locale')) {
			$locale = $request->query->get('locale');
			if (!in_array($locale, $available_locales)) {
				$locale = $available_locales[0];
			}
			$session->set('locale', $locale);
		} else {
			$locale = $session->get('locale');
		}

		Core::setLocale($locale);
	}
} 