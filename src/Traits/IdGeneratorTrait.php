<?php

namespace BrainExe\Core\Traits;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\IdGenerator;

/**
 * @api
 */
trait IdGeneratorTrait
{
    /**
     * @var IdGenerator
     */
    private $idGenerator;

    /**
     * @Inject("@IdGenerator")
     * @param IdGenerator $idGenerator
     */
    public function setIdGenerator(IdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param string $type
     * @return int
     */
    protected function generateUniqueId($type = IdGenerator::DEFAULT_TYPE)
    {
        return $this->idGenerator->generateUniqueId($type);
    }

    /**
     * @param integer $length
     * @return string
     */
    protected function generateRandomId($length = IdGenerator::ID_LENGTH)
    {
        return $this->idGenerator->generateRandomId($length);
    }
}
