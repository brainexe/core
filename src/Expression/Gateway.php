<?php

namespace BrainExe\Core\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Expression.Gateway", public=true)
 */
class Gateway
{

    const REDIS_KEY = 'expressions';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @param Entity $entity
     */
    public function save(Entity $entity)
    {
        $entity->expressionId = $entity->expressionId ?: $this->generateRandomNumericId();
        $entity->counter      = $entity->counter ?: 0;
        $entity->payload      = $entity->payload ?: [];

        $this->getRedis()->hset(
            self::REDIS_KEY,
            $entity->expressionId,
            serialize($entity)
        );
    }

    /**
     * @return Entity[]
     */
    public function getAll()
    {
        return array_map(
            function ($string) {
                return unserialize($string);
            },
            $this->getRedis()->hgetall(self::REDIS_KEY)
        );
    }

    /**
     * @param int $expressionId
     */
    public function delete($expressionId)
    {
        $this->getRedis()->hdel(self::REDIS_KEY, $expressionId);
    }
}
