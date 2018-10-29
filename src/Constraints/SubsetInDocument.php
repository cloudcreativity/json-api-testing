<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

class SubsetInDocument extends Constraint
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
     * SubsetInDocument constructor.
     *
     * @param array $expected
     *      the expected object
     * @param string $pointer
     *      the JSON pointer to the object in the JSON API document.
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
    public function evaluate($other, $description = '', $returnResult = false)
    {
        $actual = Document::cast($other)->get($this->pointer);
        $result = Compare::subset($this->expected, $actual, $this->strict);

        if ($returnResult) {
            return $result;
        }

        if (!$result) {
            $this->fail(
                $other,
                $description,
                Compare::failure($this->expected, $actual, true)
            );
        }
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
        return "the member at [{$this->pointer}] matches the subset:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

}
