<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

trait TwigTrait
{

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @Inject("@Twig")
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $name
     * @param array $context
     * @return string
     */
    public function render($name, array $context = [])
    {
        return $this->twig->render($name, $context);
    }

    /**
     * @param string $name
     * @param array $context
     * @return Response
     */
    protected function renderToResponse($name, array $context = [])
    {
        return new Response($this->render($name, $context));
    }
}
