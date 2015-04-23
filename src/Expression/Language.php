<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Service;
use Exception;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;

/**
 * @todo add support for common functions like: time() ...
 * @Service("Expression.Language", public=false)
 */
class Language extends ExpressionLanguage
{

    /**
     * {@inheritdoc}
     */
    public function __construct(ParserCacheInterface $cache = null, array $providers = [])
    {
        parent::__construct($cache, $providers);

        $functions = [
            'sprintf',
            'date',
            'time',
            'microtime',
            'rand'
        ];

        foreach ($functions as $function) {
            $this->register($function, function () use ($function) {
                $parameters = func_get_args();

                return sprintf('%s(%s)', $function, implode(', ', $parameters));
            }, function () use ($function) {
                $parameters = array_slice(func_get_args(), 1);
                return call_user_func_array($function, $parameters);
            });
        }
    }

}
