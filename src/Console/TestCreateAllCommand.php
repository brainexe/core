<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\DependencyInjection\Rebuild;
use Exception;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @Command
 * @codeCoverageIgnore
 */
class TestCreateAllCommand extends Command
{

    /**
     * Cached container builder
     * @var ContainerBuilder|null
     */
    private $containerBuilder = null;

    /**
     * @var Rebuild
     */
    private $rebuild;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('test:create:all')
            ->addArgument('root', InputArgument::OPTIONAL, 'source root directory (default: src)', 'src');
    }

    /**
     * @inject("@Core.Rebuild")
     * @param Rebuild $rebuild
     */
    public function __construct(Rebuild $rebuild)
    {
        $this->rebuild = $rebuild;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initContainerBuilder();

        $ids = $this->containerBuilder->getServiceIds();

        foreach ($ids as $serviceId) {
            try {
                $this->handleService($input, $output, $serviceId);
            } catch (InvalidArgumentException $e) {
                $output->writeln(sprintf('<error>%s: %s</error>', $serviceId, $e->getMessage()));
            }
        }
    }

    /**
     * @param string $serviceNamespace
     * @return string
     */
    private function getTestFileName($serviceNamespace)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $serviceNamespace);

        return sprintf('%sTests/%sTest.php', ROOT, $path);
    }


    /**
     * @param string $serviceId
     * @return object
     */
    private function getService($serviceId)
    {
        return $this->containerBuilder->get($serviceId);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $serviceId
     * @throws Exception
     */
    private function handleService(InputInterface $input, OutputInterface $output, $serviceId)
    {
        $serviceObject     = $this->getService($serviceId);

        $serviceReflection = new ReflectionClass($serviceObject);
        $serviceNamespace  = $serviceReflection->getName();

        $src = ROOT . $input->getArgument('root');
        if (strpos($serviceReflection->getFileName(), $src) !== 0) {
            return;
        }

        $testFileName = $this->getTestFileName($serviceNamespace);

        if (!file_exists($testFileName)) {
            $output->writeln("create: <info>$serviceId</info> - <info>" . $serviceReflection->getFileName()."<info>");

            $input = new ArrayInput(['command' => 'test:create', 'service' => $serviceId]);
            $this->getApplication()->run($input, $output);

        }
    }

    private function initContainerBuilder()
    {
        $this->containerBuilder = $this->rebuild->rebuildDIC(false);
    }
}
