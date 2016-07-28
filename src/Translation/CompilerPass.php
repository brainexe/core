<?php

namespace BrainExe\Core\Translation;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPassAnnotation("Core.Translation.CompilerPass")
 */
class CompilerPass implements CompilerPassInterface
{

    const CACHE_FILE = ROOT . 'cache/translation_token';
    const TAG = 'middleware';

    use FileCacheTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $tokens = $this->getTokens($container);

        $this->dumpTranslations($tokens);
    }

    /**
     * @param ContainerBuilder $container
     * @return string[]
     */
    private function getTokens(ContainerBuilder $container) : array
    {
        $tokens = [];

        $serviceIds = $container->getServiceIds();
        foreach ($serviceIds as $serviceId) {
            try {
                $class = $container->getDefinition($serviceId)->getClass();
                $reflection = new ReflectionClass($class);
            } catch (Exception $e) {
                continue;
            }

            $this->getTokensForService($tokens, $container, $reflection, $serviceId);
        }

        return $tokens;
    }

    /**
     * @param array $tokens
     * @param ContainerBuilder $container
     * @param ReflectionClass $reflection
     * @param string $serviceId
     */
    private function getTokensForService(
        array &$tokens,
        ContainerBuilder $container,
        ReflectionClass $reflection,
        string $serviceId
    ) {
        if ($reflection->implementsInterface(ServiceTranslationProvider::class)) {
            /** @var ServiceTranslationProvider $class */
            $service = $container->get($serviceId);

            foreach ($service->getTokens() as $token) {
                $tokens[] = $token;
            }
        } elseif ($reflection->implementsInterface(TranslationProvider::class)) {
            /** @var TranslationProvider $className */
            $className = $reflection->getName();
            foreach ($className::getTokens() as $token) {
                $tokens[] = $token;
            }
        }
    }

    /**
     * @param array $tokens
     */
    protected function dumpTranslations(array $tokens)
    {
        sort($tokens);

        $contentPhp = sprintf("return [\n    %s\n];\n", implode(",\n    ", array_map(function ($token) {
            return sprintf('_("%s")', addslashes($token));
        }, $tokens)));

        $contentHtml = sprintf("%s", implode("\n", array_map(function ($token) {
            return sprintf('<span translate>%s</span>', addslashes($token));
        }, $tokens)));

        $this->dumpCacheFile(self::CACHE_FILE, $contentPhp);
        file_put_contents(self::CACHE_FILE . '.html', $contentHtml);
        chmod(self::CACHE_FILE . '.html', 0777);
    }
}
