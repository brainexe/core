<?php

namespace BrainExe\Core\Index;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation
 */
class Controller
{

    /**
     * Deliver base HTML layout
     *
     * @return Response
     * @Route("/", name="index", methods="GET")
     * @Guest
     */
    public function index() : Response
    {
        $response = file_get_contents(ROOT . '/web/index.html');

        return new Response($response);
    }

    /**
     * @return Response
     * @Route("/robots.txt", name="robots.txt")
     * @Guest
     */
    public function robotstxt() : Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setContent("User-agent: *\nDisallow: /");

        return $response;
    }
}
