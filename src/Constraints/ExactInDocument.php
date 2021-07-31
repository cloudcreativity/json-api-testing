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
 * Class ExactInDocument
 *
 * @package CloudCreativity\JsonApi\Testing
 */
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
        $this->expected = $expected;
        $this->pointer = $pointer;
        $this->strict = $strict;
    }

    /**
     * @inheritdoc
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $actual = Document::cast($other)->get($this->pointer);
        $result = Compare::exact($this->expected, $actual, $this->strict);

        if ($returnResult) {
            return $result;
        }

        if (!$result) {
            $this->fail($other, $description, Compare::failure($this->expected, $actual));
        }

        return null;
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
