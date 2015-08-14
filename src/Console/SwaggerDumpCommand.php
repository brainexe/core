<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Application\SerializedRouteCollection;

use BrainExe\Core\Traits\ConfigTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Yaml\Dumper;

/**
 * @CommandAnnotation
 * @codeCoverageIgnore
 */
class SwaggerDumpCommand extends Command
{

    use ConfigTrait;

    /**
     * @var SerializedRouteCollection
     */
    private $routes;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swagger:dump')
            ->setDescription('Dump swagger config');
    }

    /**
     * @Inject("@Core.RouteCollection")
     * @param SerializedRouteCollection $routes
     */
    public function __construct(SerializedRouteCollection $routes)
    {
        $this->routes = $routes;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $applicationName = $this->getParameter('application.name');

        $routes = $this->routes->all();

        $dumper = new Dumper();

        $resources = [];
        foreach ($routes as $name => $route) {
            $resources[$route->getPath()][strtolower(implode(',', $route->getMethods()) ?: 'get')] = [
                'summary' => $name,
                'responses' => []
            ];
        }

        $formatted = [
            'swagger' => '2.0',
            'info'    => [
                'title'       => $applicationName,
                'description' => sprintf('%s API', $applicationName),
                'version'     =>  '1.0.0'
            ],
            'produces' => 'application/json',
            'host'     => 'localhost',
            'schemes'  => 'http',
            'paths'    => $resources
        ];

        echo $dumper->dump($formatted);
    }
}
