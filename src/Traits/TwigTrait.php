<?php

namespace BrainExe\Core\Traits;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

trait TwigTrait
{

    /**
     * @var Twig_Environment
     */
    protected $_twig;

    /**
     * @Inject("@Twig")
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->_twig = $twig;
    }

    /**
     * @param string $name
     * @param array $context
     * @return string
     */
    public function render($name, array $context = [])
    {
        return $this->_twig->render($name, $context);
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
