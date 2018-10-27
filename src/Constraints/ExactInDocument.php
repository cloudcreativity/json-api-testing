<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;

class ExactInDocument extends Constraint
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
     * ExactInDocument constructor.
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
        $expected = Arr::sortRecursive($this->expected);

        if (is_array($actual)) {
            $actual = Arr::sortRecursive($actual);
        }

        if ($this->strict) {
            $result = $actual === $expected;
        } else {
            $result = $actual == $expected;
        }

        if ($returnResult) {
            return $result;
        }

        if (!$result) {
            $f = new ComparisonFailure(
                $expected,
                $actual,
                \var_export($expected, true),
                \var_export($actual, true)
            );

            $this->fail($other, $description, $f);
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
        return "the member at [{$this->pointer}] exactly matches:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

}
