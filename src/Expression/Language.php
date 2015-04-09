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

        $this->register('sprintf', function () {
            throw new Exception('sprintf() not implemented');
        }, function ($parameters, $string) {
            unset($parameters);
            return vsprintf($string, array_slice(func_get_args(), 2));
        });
    }

}
