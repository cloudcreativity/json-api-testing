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

/**
 * Class SubsetInList
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class SubsetInList extends Constraint
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
     * SubsetInList constructor.
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
        $actual = Document::cast($other)->get($this->pointer);

        if (!is_array($actual)) {
            return false;
        }

        return collect($actual)->contains(function ($item) {
            return $this->compare($item);
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
    protected function compare($actual): bool
    {
        return Compare::subset($this->expected, $actual, $this->strict);
    }

}
