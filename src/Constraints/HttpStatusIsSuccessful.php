<?php
/*
 * Copyright 2022 Cloud Creativity Limited
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

use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Class HttpStatusIsSuccessful
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class HttpStatusIsSuccessful extends Constraint
{

    /**
     * @var string|null
     */
    private ?string $content;

    /**
     * HttpStatusIsSuccessful constructor.
     *
     * @param string|null $content
     */
    public function __construct(string $content = null)
    {
        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return 'successful';
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($other): string
    {
        if (204 === intval($other)) {
            return 'the HTTP status 204 No Content is invalid as there is content.';
        }

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
        if (!is_numeric($other)) {
            return false;
        }

        $other = (int) $other;

        if (204 === $other && !empty($this->content)) {
            return false;
        }

        return 200 <= $other && 299 >= $other;
    }

}
