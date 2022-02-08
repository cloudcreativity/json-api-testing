<?php
/*
 * Copyright 2022 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Class HttpStatusIs
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class HttpStatusIs extends Constraint
{

    /**
     * @var int
     */
    private int $expected;

    /**
     * @var mixed
     */
    private $content;

    /**
     * HttpStatusIs constructor.
     *
     * @param int $expected
     * @param mixed $content
     */
    public function __construct(int $expected, $content = null)
    {
        $this->expected = $expected;
        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return (string) $this->expected;
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($other): string
    {
        $message = "the HTTP status {$other} is {$this->toString()}";
        $document = Document::create($this->content);

        if ($document && $document->has('errors')) {
            return $message . ". The response errors were:"  . PHP_EOL . $document;
        }

        return $message;
    }

    /**
     * @param mixed $other
     * @return bool
     */
    protected function matches($other): bool
    {
        if (!is_string($other) && !is_int($other)) {
            return false;
        }

        return $this->expected === (int) $other;
    }

}
