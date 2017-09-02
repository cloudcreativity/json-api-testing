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

use PHPUnit_Framework_Assert as PHPUnit;
use stdClass;

/**
 * Class DocumentTester
 *
 * @package CloudCreativity\JsonApi
 */
class DocumentTester
{

    const KEYWORD_DATA = 'data';
    const KEYWORD_INCLUDED = 'included';
    const KEYWORD_ERRORS = 'errors';

    /**
     * @var stdClass
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
            PHPUnit::fail('Invalid response JSON: ' . json_last_error_msg());
        }

        if (!is_object($decoded)) {
            PHPUnit::fail('Invalid JSON API response content.');
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
     */
    public function assertData($message = null)
    {
        $message = $message ?: 'Document does not have a data member.';
        PHPUnit::assertObjectHasAttribute(self::KEYWORD_DATA, $this->document, $message);

        return $this;
    }

    /**
     * Assert that the data member is an object and return a resource tester.
     *
     * @param string|null $message
     * @return ResourceObjectTester
     */
    public function assertResource($message = null)
    {
        $message = $message ?: 'Document does not have a resource in its data member.';
        $resource = $this->getData();

        PHPUnit::assertInternalType('object', $resource, $message);

        return new ResourceObjectTester($resource);
    }

    /**
     * Assert that the data member is a collection, and return it as a resource collection tester.
     *
     * @param string|null $message
     * @return ResourceObjectsTester
     */
    public function assertResourceCollection($message = null)
    {
        $message = $message ?: 'Document does not have a resource collection in its data member.';
        $collection = $this->getData();

        PHPUnit::assertInternalType('array', $collection, $message);

        return new ResourceObjectsTester($collection);
    }

    /**
     * Assert that the included member is an array, and return it as a resource collection tester.
     *
     * @param string|null $message
     * @return ResourceObjectsTester
     */
    public function assertIncluded($message = null)
    {
        $message = $message ?: 'Document does not contain an included member.';
        PHPUnit::assertObjectHasAttribute(self::KEYWORD_INCLUDED, $this->document, $message);

        return new ResourceObjectsTester((array) $this->document->{self::KEYWORD_INCLUDED});
    }

    /**
     * Assert that the document has an errors key, and return an errors tester.
     *
     * @param string|null $message
     * @return ErrorsTester
     */
    public function assertErrors($message = null)
    {
        $message = $message ?: 'Document does not contain errors.';
        PHPUnit::assertObjectHasAttribute(self::KEYWORD_ERRORS, $this->document, $message);

        return new ErrorsTester((array) $this->document->{self::KEYWORD_ERRORS});
    }
}
