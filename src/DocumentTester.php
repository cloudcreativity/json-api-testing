<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing;

use PHPUnit\Framework\Assert;
use stdClass;

/**
 * Class DocumentTester
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class DocumentTester extends ObjectTester
{

    /**
     * @var stdClass
     * @deprecated
     */
    private $document;

    /**
     * Create a document tester from a raw HTTP response content.
     *
     * @param string $responseContent
     * @return DocumentTester
     */
    public static function create($responseContent)
    {
        $decoded = json_decode($responseContent);

        if (JSON_ERROR_NONE !== json_last_error()) {
            Assert::fail('Invalid response JSON: ' . json_last_error_msg());
        }

        if (!is_object($decoded)) {
            Assert::fail('Invalid JSON API response content.');
        }

        return new self($decoded);
    }

    /**
     * DocumentTester constructor.
     *
     * @param stdClass $document
     */
    public function __construct(stdClass $document)
    {
        parent::__construct($document);
        $this->document = $document;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return isset($this->document->{self::KEYWORD_DATA}) ?
            $this->document->{self::KEYWORD_DATA} : null;
    }

    /**
     * @return mixed
     */
    public function getIncluded()
    {
        return isset($this->document->{self::KEYWORD_INCLUDED}) ?
            $this->document->{self::KEYWORD_INCLUDED} : null;
    }

    /**
     * Assert that the document has a data member.
     *
     * @param string|null $message
     * @return $this
     * @deprecated use `assertHasData`
     */
    public function assertData($message = null)
    {
        return $this->assertHasData($message);
    }

    /**
     * Assert that the data member exists and it is null.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertDataNull($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_DATA, 'null', $message);

        return $this;
    }

    /**
     * Assert that the data member is a resource identifier.
     *
     * @param string|null $message
     * @return ResourceIdentifierTester
     */
    public function assertResourceIdentifier($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_DATA, 'object', $message);
        $identifier = new ResourceIdentifierTester($this->object->{self::KEYWORD_DATA});

        /** Check that these members do not exist, otherwise it's a resource not a resource identifier. */
        $identifier->assertMemberMissing(self::KEYWORD_ATTRIBUTES, $message);
        $identifier->assertMemberMissing(self::KEYWORD_RELATIONSHIPS, $message);

        return $identifier;
    }

    /**
     * Assert that the data member is an object and return a resource tester.
     *
     * @param string|null $message
     * @return ResourceObjectTester
     */
    public function assertResource($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_DATA, 'object', $message);

        return new ResourceObjectTester($this->object->{self::KEYWORD_DATA});
    }

    /**
     * Assert that the data member is a collection, and return it as a resource collection tester.
     *
     * @param string|null $message
     * @return ResourceObjectsTester
     */
    public function assertResourceCollection($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_DATA, 'array', $message);

        return new ResourceObjectsTester($this->object->{self::KEYWORD_DATA});
    }

    /**
     * Assert that the included member is an array, and return it as a resource collection tester.
     *
     * @param string|null $message
     * @return ResourceObjectsTester
     */
    public function assertIncluded($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_INCLUDED, 'array', $message);

        return new ResourceObjectsTester($this->object->{self::KEYWORD_INCLUDED});
    }

    /**
     * Assert that the document has an errors key, and return an errors tester.
     *
     * @param string|null $message
     * @return ErrorsTester
     */
    public function assertErrors($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_ERRORS, 'array', $message);

        return new ErrorsTester($this->object->{self::KEYWORD_ERRORS});
    }

    /**
     * Assert that the document does not have errors, and output the errors if it does.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertNoErrors($message = null)
    {
        $message = $message ?: "Unexpected JSON API errors.\n" . json_encode($this->object, JSON_PRETTY_PRINT);
        $this->assertMemberMissing(self::KEYWORD_ERRORS, $message);

        return $this;
    }
}
