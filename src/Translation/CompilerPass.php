<?php

namespace BrainExe\Core\Translation;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Exception;
use Generator;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Translation\Token;

/**
 * @CompilerPassAnnotation
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
        $tokens = iterator_to_array($this->getTokens($container));

        $this->dumpTranslations($tokens);
    }

    /**
     * @param ContainerBuilder $container
     * @return Generator|Token[]
     */
    private function getTokens(ContainerBuilder $container) : Generator
    {
        $serviceIds = $container->getServiceIds();
        foreach ($serviceIds as $serviceId) {
            try {
                $class = $container->findDefinition($serviceId)->getClass();
                $reflection = new ReflectionClass($class);
            } catch (Exception $e) {
                continue;
            }

            yield from $this->getTokensForService($container, $reflection, $serviceId);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param ReflectionClass $reflection
     * @param string $serviceId
     * @return Generator|Token[]
     */
    private function getTokensForService(
        ContainerBuilder $container,
        ReflectionClass $reflection,
        string $serviceId
    ) : Generator {
        if ($reflection->implementsInterface(ServiceTranslationProvider::class)) {
            /** @var ServiceTranslationProvider $class */
            $service = $container->get($serviceId);

            foreach ($service->getTokens() as $token) {
                if (!$token instanceof Token) {
                    $token = new Token($token);
                }

                yield $token->token => $token;
            }
        } elseif ($reflection->implementsInterface(TranslationProvider::class)) {
            /** @var TranslationProvider $className */
            $className = $reflection->getName();
            foreach ($className::getTokens() as $token) {
                if (!$token instanceof Token) {
                    $token = new Token($token);
                }

                yield $token->token => $token;
            }
        }
    }

    /**
     * @param array $tokens
     */
    protected function dumpTranslations(array $tokens)
    {
        ksort($tokens);

        $contentPhp = sprintf("return [\n    %s\n];\n", implode(",\n    ", array_map(function (Token $token) {
            return sprintf('_("%s")', addslashes($token->token));
        }, $tokens)));

        $contentHtml = sprintf("%s", implode("\n", array_map(function (Token $token) {
            return sprintf('<span translate>%s</span>', addslashes($token->token));
        }, $tokens)));

        $this->dumpCacheFile(self::CACHE_FILE, $contentPhp);
        file_put_contents(self::CACHE_FILE . '.html', $contentHtml);
    }
}
