<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;

class ExactInList extends SubsetInList
{

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the list at [{$this->pointer}] contains the values:" . PHP_EOL
            . $this->toString() . PHP_EOL . PHP_EOL
            . "within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

    /**
     * @param $actual
     * @return bool
     */
    protected function compare($actual): bool
    {
        return Compare::exact($this->expected, $actual, $this->strict);
    }

}
