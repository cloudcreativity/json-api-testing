<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

class ExactInDocument extends Constraint
{

    /**
     * @var mixed
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
     * ExactInDocument constructor.
     *
     * @param mixed $expected
     *      the expected value
     * @param string $pointer
     *      the JSON pointer to the object in the JSON API document.
     * @param bool $strict
     */
    public function __construct($expected, string $pointer, bool $strict = true)
    {
        parent::__construct();
        $this->expected = $expected;
        $this->pointer = $pointer;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function evaluate($other, $description = '', $returnResult = false)
    {
        $actual = Document::cast($other)->get($this->pointer);
        $result = Compare::exact($this->expected, $actual, $this->strict);

        if ($returnResult) {
            return $result;
        }

        if (!$result) {
            $this->fail($other, $description, Compare::failure($this->expected, $actual));
        }
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Compare::stringify($this->expected);
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the member at [{$this->pointer}] exactly matches:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

}
