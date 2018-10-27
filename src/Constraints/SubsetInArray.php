<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Constraint\Constraint;

class SubsetInArray extends Constraint
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
     * ArrayContainsSubset constructor.
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
        $actual = Document::cast($other)->get($this->pointer);

        return collect((array) $actual)->contains(function ($item) {
            return $this->compare($item);
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
        return "the array at [{$this->pointer}] contains the subset:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

    /**
     * @param $actual
     * @return bool
     */
    private function compare($actual): bool
    {
        $patched = \array_replace_recursive((array) $actual, $this->expected);

        if ($this->strict) {
            return $actual === $patched;
        }

        return $actual == $patched;
    }

}
