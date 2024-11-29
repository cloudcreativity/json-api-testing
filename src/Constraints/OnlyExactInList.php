<?php
/*
 * Copyright 2024 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Class OnlyExactInList
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class OnlyExactInList extends OnlySubsetsInList
{

    /**
     * @inheritdoc
     */
    protected function fail($other, $description, ?ComparisonFailure $comparisonFailure = null): never
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
        return Compare::exact($expected, $actual, $this->strict);
    }

}
