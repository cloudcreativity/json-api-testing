<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;

class IdentifiersInDocument extends SubsetInDocument
{

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the member at [{$this->pointer}] matches the resource identifiers:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

    /**
     * @param array $expected
     * @param $actual
     * @param bool $strict
     * @return bool
     */
    protected function compare(array $expected, $actual, bool $strict): bool
    {
        if (!parent::compare($expected, $actual, $strict)) {
            return false;
        }

        return collect($actual)->every(function (array $identifier) {
            return Compare::resourceIdentifier($identifier);
        });
    }
}
