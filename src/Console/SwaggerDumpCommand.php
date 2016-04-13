<?php

namespace BrainExe\Core\Console;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Application\SerializedRouteCollection;

use BrainExe\Core\Traits\ConfigTrait;
use Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route;
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
     * @param SerializedRouteCollection $rebuild
     */
    public function __construct(SerializedRouteCollection $rebuild)
    {
        $this->routes = $rebuild;

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

        $resources = $this->getResources($routes);

        $url = parse_url($this->getParameter('application.url'));

        $formatted = [
            'swagger' => '2.0',
            'info'    => [
                'title'       => $applicationName,
                'description' => sprintf('%s API', $applicationName),
                'version'     =>  '1.0.0'
            ],
            'consumes' => ['application/json'],
            'produces' => ['application/json', 'text/html'],
            'host'     => $url['host'],
            'schemes'  => [$url['scheme']],
            'securityDefinitions' => [
                'token' => [
                    'type'        => 'apiKey',
                    'description' => 'Given API Token',
                    'name'        => 'Token',
                    'in'          => 'Header'
                ]
            ],
            'security' => [
                'token' => [
                    'all'
                ]
            ],
            'paths' => $resources,
        ];

        echo $dumper->dump($formatted, 4);
        echo PHP_EOL;
    }

    /**
     * @param Route $route
     * @return Generator
     */
    private function getParameters(Route $route)
    {
        /** @var CompiledRoute $compiled */
        $compiled = $route->compile();

        foreach ($compiled->getVariables() as $name) {
            yield [
                'name'     => $name,
                'type'     => 'string',
                'required' => true,
                'in'       => 'path'
            ];
        }
    }

    /**
     * @param array $routes
     * @return array
     */
    protected function getResources(array $routes) : array
    {
        $resources = [];
        foreach ($routes as $name => $route) {
            $parameters = iterator_to_array($this->getParameters($route));

            $data = [
                'summary' => $name,
                'responses' => [
                    200 => [
                        'description' => 'OK'
                    ]
                ]
            ];

            if ($parameters) {
                $data['parameters'] = $parameters;
            }

            $resources[$route->getPath()][strtolower(implode(',', $route->getMethods()) ?: 'get')] = $data;
        }
        return $resources;
    }
}
