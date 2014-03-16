<?php

namespace Matze\Core\Middleware;

use Matze\Core\Core;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @Middleware(priority=4)
 */
class LocaleMiddleware extends AbstractMiddleware {

	/**
	 * {@inheritdoc}
	 */
	public function processRequest(Request $request, Route $route) {
		$session = $request->getSession();

		if ($request->query->has('locale')) {
			$locale = $request->query->get('locale');
			// TODO validate
			$session->set('locale', $locale);
		} else {
			$locale = $session->get('locale');
		}

		Core::setLocale($locale);
	}
} 