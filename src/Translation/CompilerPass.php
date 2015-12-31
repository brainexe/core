<?php

namespace BrainExe\Core\Translation;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @CompilerPassAnnotation
 */
class CompilerPass implements CompilerPassInterface
{

    const CACHE_FILE = ROOT . 'cache/translation_token';

    use FileCacheTrait;

    const TAG = 'middleware';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->getServiceIds();

        $tokens = [];
        foreach ($serviceIds as $serviceId) {
            try {
                $class = $container->getDefinition($serviceId)->getClass();
                $reflection = new ReflectionClass($class);
            } catch (Exception $e) {
                continue;
            }

            if ($reflection->implementsInterface(TranslationProvider::class)) {
                /** @var TranslationProvider $class */
                foreach ($class::getTokens() as $token) {
                    $tokens[] = $token;
                }
            }
        }

        sort($tokens);

        $contentPhp = sprintf("return [\n    %s\n];\n", implode(",\n    ", array_map(function ($token) {
            return sprintf('_("%s")', addslashes($token));
        }, $tokens)));

        $contentHtml = sprintf("%s", implode("\n", array_map(function ($token) {
            return sprintf('<span translate>%s</span>', addslashes($token));
        }, $tokens)));

        $this->dumpCacheFile(self::CACHE_FILE, $contentPhp);
        file_put_contents(self::CACHE_FILE . '.html', $contentHtml);
    }
}
