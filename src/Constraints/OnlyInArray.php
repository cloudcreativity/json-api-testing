<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

class OnlyInArray extends Constraint
{

    /**
     * @var array
     */
    private $expected;

    /**
     * @var string
     */
    private $pointer;

    /**
     * @var bool
     */
    private $strict;

    /**
     * OnlyInArray constructor.
     *
     * @param array $expected
     *      the expected object
     * @param string $pointer
     *      the JSON pointer to the array in the JSON API document.
     * @param bool $strict
     */
    public function __construct(array $expected, string $pointer, bool $strict = true)
    {
        parent::__construct();
        $this->expected = $expected;
        $this->pointer = $pointer;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function matches($other): bool
    {
        $other = Document::cast($other)->get($this->pointer);

        $allValid = collect((array) $other)->every(function ($item) {
            return $this->expected((array) $item);
        });

        if (!$allValid) {
            return $allValid;
        }

        return collect($this->expected)->every(function ($expected) use ($other) {
            return $this->exists($expected, $other);
        });
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Document::cast($this->expected)->toString();
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the array at [{$this->pointer}] only contains the subsets:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

    /**
     * Is the actual subset expected?
     *
     * @param array $actual
     * @return bool
     */
    private function expected(array $actual): bool
    {
        return collect($this->expected)->contains(function ($expected) use ($actual) {
            return $this->compare($expected, $actual);
        });
    }

    /**
     * Does the expected subset exist in the actual array?
     *
     * @param array $expected
     * @param mixed $actual
     * @return bool
     */
    private function exists(array $expected, $actual): bool
    {
        return collect((array) $actual)->contains(function ($item) use ($expected) {
            return $this->compare($expected, (array) $item);
        });
    }

    /**
     * @param array $expected
     * @param array $actual
     * @return bool
     */
    private function compare(array $expected, array $actual): bool
    {
        $patched = \array_replace_recursive($actual, $expected);

        if ($this->strict) {
            return $actual === $patched;
        }

        return $actual == $patched;
    }

}
