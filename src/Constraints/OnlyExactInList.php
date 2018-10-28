<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;

class OnlyExactInList extends OnlySubsetsInList
{

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
        return Compare::exact($expected, $actual, $this->strict);
    }

}
