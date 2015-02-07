<?php

namespace BrainExe\Core\Console;

/**
 * @codeCoverageIgnore
 */
class TestData
{
    public $setterCalls          = [];
    public $defaultTests         = [];
    public $mockProperties       = [];
    public $localMocks           = [];
    public $constructorArguments = [];

    /**
     * @var string[]
     */
    private $useStatements = [];

    /**
     * @param string $class
     * @param string|null $alias
     */
    public function addUse($class, $alias = null)
    {
        if ($alias) {
            $this->useStatements[$alias] = $class;
        } else {
            $this->useStatements[] = $class;
        }
    }

    /**
     * @return string
     */
    public function renderUse()
    {
        $parts = [];

        asort($this->useStatements);

        foreach ($this->useStatements as $alias => $class) {
            if (is_numeric($alias)) {
                $parts[] = sprintf('use %s;', $class);
            } else {
                $parts[] = sprintf('use %s as %s;', $class, $alias);
            }
        }

        return implode("\n", $parts);
    }
}
