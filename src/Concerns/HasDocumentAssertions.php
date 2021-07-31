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

namespace CloudCreativity\JsonApi\Testing\Concerns;

use CloudCreativity\JsonApi\Testing\Assert;

/**
 * Trait HasDocumentAssertions
 *
 * @package CloudCreativity\JsonApi\Testing
 */
trait HasDocumentAssertions
{

    /**
     * Assert that the value at the pointer has the expected JSON API resource.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the resource object is expected in the document.
     * @param string $message
     * @return $this
     */
    public function assertResource(
        string $type,
        string $id,
        string $pointer = '/data',
        string $message = ''
    ): self
    {
        Assert::assertResource($this, $type, $id, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the value at the pointer has the expected JSON API resource identifier.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the resource object is expected in the document.
     * @param string $message
     * @return $this
     */
    public function assertIdentifier(
        string $type,
        string $id,
        string $pointer = '/data',
        string $message = ''
    ): self
    {
        Assert::assertIdentifier($this, $type, $id, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the expected value is in the document at the specified path.
     *
     * @param array|null $expected
     *      the expected value.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @param string $message
     * @return $this
     */
    public function assertExact(
        $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertExact($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that the value at the specified path is not the expected value.
     *
     * @param $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertNotExact(
        $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertNotExact($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that the expected hash is in the document at the specified path.
     *
     * @param array $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @param string $message
     * @return $this
     */
    public function assertHash(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertHash($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that the member contains a null value.
     *
     * @param string $pointer
     * @param string $message
     * @return $this
     */
    public function assertNull(string $pointer = '/data', string $message = ''): self
    {
        Assert::assertNull($this, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the member contains an empty list.
     *
     * @param string $pointer
     * @param string $message
     * @return $this
     */
    public function assertListEmpty(string $pointer = '/data', string $message = ''): self
    {
        Assert::assertListEmpty($this, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the member does not contain an empty list.
     *
     * @param string $pointer
     * @param string $message
     * @return $this
     */
    public function assertListNotEmpty(string $pointer = '/data', string $message = ''): self
    {
        Assert::assertListNotEmpty($this, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the member is an empty list or does not exist (is missing).
     *
     * @param string $pointer
     * @param string $message
     * @return $this
     */
    public function assertListEmptyOrMissing(string $pointer = '/data', string $message = ''): self
    {
        Assert::assertListEmptyOrMissing($this, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the member exists and is not an empty list.
     *
     * @param string $pointer
     * @param string $message
     * @return $this
     */
    public function assertListNotEmptyOrMissing(string $pointer = '/data', string $message = ''): self
    {
        Assert::assertListNotEmptyOrMissing($this, $pointer, $message);

        return $this;
    }

    /**
     * Assert that list in the document only contains the specified hashes.
     *
     * This assertion does not check that the expected and actual lists are in the same order.
     * To assert the order, use `assertListInOrder`.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertList(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertList($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that a list in the document only contains the specified values.
     *
     * This assertion does not check that the expected and actual lists are in the same order.
     * To assert the order, use `assertExactListInOrder`.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactList(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertExactList($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that a list in the document contains the hashes in the specified order.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertListInOrder(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertListInOrder($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that an array in the document contains the values in the specified order.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactListInOrder(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertExactListInOrder($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that list in the document only contains the specified hashes.
     *
     * Asserting that a list contains only identifiers will fail if any of the items in the
     * list is a resource object. I.e. to pass as an identifier, it must not contain
     * `attributes` and/or `relationships` members.
     *
     * This assertion does not check that the expected and actual lists are in the same order.
     * To assert the order, use `assertListInOrder`.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertIdentifiersList(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertIdentifiersList($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that a list in the document contains the identifiers in the specified order.
     *
     * Asserting that a list contains only identifiers will fail if any of the items in the
     * list is a resource object. I.e. to pass as an identifier, it must not contain
     * `attributes` and/or `relationships` members.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertIdentifiersListInOrder(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertIdentifiersListInOrder($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that the document has a list containing the expected resource.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the array is expected in the document.
     * @param string $message
     * @return $this
     */
    public function assertListContainsResource(
        string $type,
        string $id,
        string $pointer = '/data',
        string $message = ''
    ): self
    {
        Assert::assertListContainsResource($this, $type, $id, $pointer, $message);

        return $this;
    }

    /**
     * Assert that the document has a list containing the expected resource identifier.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the array is expected in the document.
     * @param string $message
     * @return $this
     */
    public function assertListContainsIdentifier(
        string $type,
        string $id,
        string $pointer = '/data',
        string $message = ''
    ): self
    {
        Assert::assertListContainsIdentifier($this, $type, $id, $pointer, $message);

        return $this;
    }

    /**
     * Assert that a list in the document at the specified path contains the expected hash.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertListContainsHash(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertListContainsHash($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that a list in the document at the specified path contains the expected value.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertListContainsExact(
        array $expected,
        string $pointer = '/data',
        bool $strict = true,
        string $message = ''
    ): self
    {
        Assert::assertListContainsExact($this, $expected, $pointer, $strict, $message);

        return $this;
    }

    /**
     * Assert that the document's included member matches the expected array.
     *
     * This does not assert the order of the included member because there is no significance to
     * the order of resources in the included member.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertIncluded(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertIncluded($this, $expected, $strict, $message);

        return $this;
    }

    /**
     * Assert that the expected identifier is included in the document.
     *
     * @param string $type
     * @param string $id
     * @param string $message
     * @return $this
     */
    public function assertIncludedContainsResource(string $type, string $id, string $message = ''): self
    {
        Assert::assertIncludedContainsResource($this, $type, $id, $message);

        return $this;
    }

    /**
     * Assert that the included member contains the supplied hash.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertIncludedContainsHash(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertIncludedContainsHash($this, $expected, $strict, $message);

        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function assertNoneIncluded(string $message = ''): self
    {
        Assert::assertNoneIncluded($this, $message);

        return $this;
    }

    /**
     * Assert that the top-level meta matches the expected values.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertMeta(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertHash($this, $expected, '/meta', $strict, $message);

        return $this;
    }

    /**
     * Assert that the top-level meta is exactly the expected meta.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactMeta(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertExact($this, $expected, '/meta', $strict, $message);

        return $this;
    }

    /**
     * Assert that the top-level links match the expected values.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertLinks(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertHash($this, $expected, '/links', $strict, $message);

        return $this;
    }

    /**
     * Assert that the top-level links are exactly the expected links.
     *
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactLinks(array $expected, bool $strict = true, string $message = ''): self
    {
        Assert::assertExact($this, $expected, '/links', $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains a single error that matches the supplied error.
     *
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertError(array $error, bool $strict = true, string $message = ''): self
    {
        Assert::assertError($this, $error, $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains a single error that exactly matches the supplied error.
     *
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactError(array $error, bool $strict = true, string $message = ''): self
    {
        Assert::assertExactError($this, $error, $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains the supplied error within its errors member.
     *
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertHasError(array $error, bool $strict = true, string $message = ''): self
    {
        Assert::assertHasError($this, $error, $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains the exact supplied error within its errors member.
     *
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertHasExactError(array $error, bool $strict = true, string $message = ''): self
    {
        Assert::assertHasExactError($this, $error, $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains the supplied errors.
     *
     * This does not assert the order of the errors, as the error order does not have any significance.
     *
     * @param array $errors
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertErrors(array $errors, bool $strict = true, string $message = ''): self
    {
        Assert::assertErrors($this, $errors, $strict, $message);

        return $this;
    }

    /**
     * Assert the document contains the exact supplied errors.
     *
     * This does not assert the order of the errors, as the error order does not have any significance.
     *
     * @param array $errors
     * @param bool $strict
     * @param string $message
     * @return $this
     */
    public function assertExactErrors(array $errors, bool $strict = true, string $message = ''): self
    {
        Assert::assertExactErrors($this, $errors, $strict, $message);

        return $this;
    }
}
