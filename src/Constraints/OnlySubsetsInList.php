<?php
/*
 * Copyright 2021 Cloud Creativity Limited
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Class OnlySubsetsInList
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class OnlySubsetsInList extends Constraint
{

    /**
     * @var array
     */
    protected $expected;

    /**
     * @var string
     */
    protected $pointer;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * OnlySubsetsInList constructor.
     *
     * @param array $expected
     *      the expected object
     * @param string $pointer
     *      the JSON pointer to the array in the JSON API document.
     * @param bool $strict
     */
    public function __construct(array $expected, string $pointer, bool $strict = true)
    {
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

        if (!is_array($other)) {
            return false;
        }

        $allValid = collect($other)->every(function ($item) {
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
        return Compare::stringify($this->expected);
    }

    /**
     * @inheritdoc
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null): void
    {
        if (!$comparisonFailure) {
            $comparisonFailure = Compare::failure(
                $this->expected,
                Document::cast($other)->get($this->pointer),
                true
            );
        }

        parent::fail($other, $description, $comparisonFailure);
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
    protected function expected(array $actual): bool
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
    protected function exists(array $expected, $actual): bool
    {
        if (!is_array($actual)) {
            return false;
        }

        return collect($actual)->contains(function ($item) use ($expected) {
            return $this->compare($expected, $item);
        });
    }

    /**
     * @param array $expected
     * @param mixed $actual
     * @return bool
     */
    protected function compare(array $expected, $actual): bool
    {
        return Compare::subset($expected, $actual, $this->strict);
    }

}
