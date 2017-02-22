<?php

namespace BrainExe\Core;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Annotations\Builder\ServiceDefinition;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\IndexedReader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Config\Loader\Loader as ConfigLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AnnotationLoader extends ConfigLoader
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var ServiceDefinition[]
     */
    private $builders = [];

    /**
     * @param ContainerBuilder $container
     * @param ServiceDefinition[] $builders
     */
    public function __construct(ContainerBuilder $container, array $builders = [])
    {
        $this->container = $container;
        $this->builders  = $builders;
        $this->reader    = new IndexedReader(new AnnotationReader());

        AnnotationRegistry::registerLoader(function ($class) {
            return class_exists($class);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function load($path, $type = null)
    {
        $includedFiles = $this->includeFiles($path);
        $this->processFiles($includedFiles);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_dir($resource);
    }

    /**
     * @param ReflectionClass $reflection
     * @return array
     */
    private function loadDefinition(ReflectionClass $reflection)
    {
        $annotation = $this->reader->getClassAnnotation($reflection, Service::class);
        $definitions = [];

        /** @var ServiceDefinition $builder */
        if ($annotation) {
            $annotationClass = get_class($annotation);

            $class = $reflection->getName();
            $definition = $this->container->register($class, $class);

            if (isset($this->builders[$annotationClass])) {
                $builder = $this->builders[$annotationClass];
            } else {
                /** @var string|Service $annotationClass */
                $this->builders[$annotationClass] = $builder = $annotationClass::getBuilder(
                    $this->container,
                    $this->reader
                );
            }

            list($alias) = $builder->build($reflection, $annotation, $definition);
            if ($alias && $alias !== $class) {
                $this->container->setAlias($alias, $class);
            }
        }

        return $definitions;
    }

    /**
     * @param string $path
     * @return string[]
     */
    private function includeFiles(string $path) : array
    {
        $includedFiles = [];

        /** @var SplFileInfo[] $directoryIterator */
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($directoryIterator as $fileInfo) {
            if ($fileInfo->getExtension() === 'php') {
                $included = $this->includeFile($fileInfo);
                $includedFiles[$included] = true;
            }
        }

        return $includedFiles;
    }

    /**
     * @param string[] $includedFiles
     */
    private function processFiles(array $includedFiles)
    {
        $declaredClasses = get_declared_classes();
        foreach ($declaredClasses as $className) {
            $reflection = new ReflectionClass($className);
            $filename   = $reflection->getFileName();

            if (isset($includedFiles[$filename])) {
                $this->loadDefinition($reflection);
            }
        }
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return string
     */
    private function includeFile(SplFileInfo $fileInfo)
    {
        $sourceFile = $fileInfo->getRealPath();

        require_once $sourceFile;

        return $sourceFile;
    }
}
