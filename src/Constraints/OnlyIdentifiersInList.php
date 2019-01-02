<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use SebastianBergmann\Comparator\ComparisonFailure;

class OnlyIdentifiersInList extends OnlySubsetsInList
{

    /**
     * @inheritdoc
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null): void
    {
        $comparisonFailure = Compare::failure(
            $this->expected,
            Document::cast($other)->get($this->pointer)
        );

        parent::fail($other, $description, $comparisonFailure);
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the list at [{$this->pointer}] only contains the values:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

    /**
     * @param array $expected
     * @param mixed $actual
     * @return bool
     */
    protected function compare(array $expected, $actual): bool
    {
        if (!Compare::subset($expected, $actual, $this->strict)) {
            return false;
        }

        return Compare::resourceIdentifier($actual);
    }

}
