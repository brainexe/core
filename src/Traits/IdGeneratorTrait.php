<?php

namespace BrainExe\Core\Traits;

use BrainExe\Core\Annotations\Inject;
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
     * @Inject
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
    protected function generateUniqueId(string $type = IdGenerator::DEFAULT_TYPE) : int
    {
        return $this->idGenerator->generateUniqueId($type);
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateRandomId(int $length = IdGenerator::ID_LENGTH) : string
    {
        return $this->idGenerator->generateRandomId($length);
    }
}
