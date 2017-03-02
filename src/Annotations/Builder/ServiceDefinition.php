<?php

namespace BrainExe\Core\Annotations\Builder;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Matthias Dötsch <matze@mdoetsch.de>
 */
class ServiceDefinition
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param ContainerBuilder $container
     * @param Reader $reader
     */
    public function __construct(ContainerBuilder $container, Reader $reader)
    {
        $this->reader = $reader;
        $this->container = $container;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param Service $annotation
     * @param Definition $definition
     * @return array
     */
    public function build(ReflectionClass $reflectionClass, Service $annotation, Definition $definition)
    {
        $constructor = $reflectionClass->getConstructor();
        if (null !== $constructor) {
            $this->processConstructor($constructor, $definition);
        }

        $this->processService($annotation, $definition);
        $this->processMethods($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC), $definition);
        $this->processTags($reflectionClass, $annotation, $definition);

        $serviceId = $annotation->value ?: $annotation->name ?: $reflectionClass->getName();

        return [$this->setupDefinition($definition, $serviceId) ?? $serviceId, $definition];
    }

    /**
     * @param Definition $definition
     * @return string null
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        return null;
    }

    /**
     * @param string|string[] $value
     * @return Reference[]|Reference|Expression
     */
    private function resolveServices($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'resolveServices'], $value);
        } elseif ('=' === $value[0]) {
            return new Expression(mb_substr($value, 1));
        } elseif ('@' === $value[0]) {
            return $this->getValueReference($value);
        }

        return $value;
    }

    /**
     * @param ReflectionMethod $constructor
     * @param Definition $definition
     */
    protected function processConstructor(ReflectionMethod $constructor, Definition $definition)
    {
        if ($annotation = $this->reader->getMethodAnnotation($constructor, Inject::class)) {
            /** @var Inject $annotation */
            $arguments = $this->extractArguments(
                $constructor,
                $annotation,
                $definition->getArguments()
            );

            $definition->setArguments($arguments);
        } elseif ($constructor->getNumberOfParameters() > 0) {
            $definition->setArguments(
                $this->resolveArguments($constructor)
            );

            $definition->setAutowired(true);
        }
    }

    /**
     * @param ReflectionMethod[] $methods
     * @param Definition $definition
     */
    protected function processMethods(array $methods, Definition $definition)
    {
        foreach ($methods as $method) {
            if ($method->isConstructor()) {
                continue;
            }
            $this->processMethod($definition, $method);
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param Inject $annotation
     * @param array $arguments
     * @return array
     */
    private function extractArguments(
        ReflectionMethod $method,
        Inject $annotation,
        array $arguments = []
    ) : array {
        $values = [];
        if (is_string($annotation->value)) {
            $values = [$annotation->value];
        } elseif (is_array($annotation->value)) {
            $values = $annotation->value;
        }

        return $this->resolveArguments($method, $values, $arguments);
    }

    /**
     * @param ReflectionMethod $method
     * @param array $values
     * @param array $arguments
     * @return array
     */
    private function resolveArguments(
        ReflectionMethod $method,
        array $values = [],
        array $arguments = []
    ) : array {
        foreach ($method->getParameters() as $index => $parameter) {
            $name = $parameter->getName();
            if (!empty($values[$name])) {
                $arguments[$index] = $this->resolveServices($values[$name]);
            } elseif (isset($values[$index])) {
                $arguments[$index] = $this->resolveServices($values[$index]);
            } elseif (!isset($arguments[$index])) {
                $parameterClass = $parameter->getClass();
                if ($parameterClass) {
                    $arguments[$index] = new Reference($parameterClass->getName());
                }
            }
        }

        return $arguments;
    }

    /**
     * @param Service $annotation
     * @param Definition $definition
     */
    private function processService(Service $annotation, Definition $definition)
    {
        $definition->setAutowired(true);
        $definition->setPublic($annotation->public);
        $definition->setLazy($annotation->lazy);
        $definition->setShared($annotation->shared);
        $definition->setSynthetic($annotation->synthetic);
        $definition->setAbstract($annotation->abstract);
        $this->processConfigurator($annotation, $definition);

        if (isset($annotation->factory)) {
            $definition->setFactory($annotation->factory);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param Service $annotation
     * @param Definition $definition
     */
    private function processTags(
        ReflectionClass $reflectionClass,
        Service $annotation,
        Definition $definition
    ) {
        if (empty($annotation->tags)) {
            return;
        }

        foreach ($annotation->tags as $tag) {
            if (!isset($tag['name'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'A "tags" entry is missing a "name" key must be an array for class "%s" in %s.',
                        $reflectionClass->getName(),
                        $reflectionClass->getFileName()
                    )
                );
            }
            $name = $tag['name'];
            unset($tag['name']);

            $definition->addTag($name, $tag);
        }
    }

    /**
     * @param string $value
     * @return Reference
     */
    private function getValueReference($value) : Reference
    {
        if (0 === strpos($value, '@?')) {
            $value           = substr($value, 2);
            $invalidBehavior = ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
        } else {
            $value           = substr($value, 1);
            $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
        }

        // mark reference as strict
        if ('=' === substr($value, -1)) {
            $value  = substr($value, 0, -1);
            $strict = false;
        } else {
            $strict = true;
        }

        return new Reference($value, $invalidBehavior, $strict);
    }

    /**
     * @param Service $annotation
     * @param Definition $definition
     */
    private function processConfigurator(Service $annotation, Definition $definition)
    {
        if (!isset($annotation->configurator)) {
            return;
        }

        if (is_string($annotation->configurator)) {
            $definition->setConfigurator($annotation->configurator);
        } else {
            $definition->setConfigurator([
                $this->resolveServices($annotation->configurator[0]),
                $annotation->configurator[1]
            ]);
        }
    }

    /**
     * @param Definition $definition
     * @param ReflectionMethod $method
     */
    protected function processMethod(Definition $definition, ReflectionMethod $method)
    {
        /** @var Inject $annotation */
        $annotation = $this->reader->getMethodAnnotation($method, Inject::class);
        if ($annotation) {
            $arguments = $this->extractArguments(
                $method,
                $annotation
            );
            $definition->addMethodCall($method->getName(), $arguments);
        }
    }
}
