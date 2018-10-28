<?php

namespace CloudCreativity\JsonApi\Testing\Concerns;

use CloudCreativity\JsonApi\Testing\Assert;

trait HasDocumentAssertions
{

    /**
     * Assert that the value at the pointer has the expected JSON API identifier.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the resource object is expected in the document.
     * @return $this
     */
    public function assertIdentifier(string $type, string $id, string $pointer = '/data'): self
    {
        Assert::assertIdentifier($this, $type, $id, $pointer);

        return $this;
    }

    /**
     * Assert that the expected value is in the document at the specified path.
     *
     * @param array|null $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return $this
     */
    public function assertExact($expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertExact($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that the value at the specified path is not the expected value.
     *
     * @param $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertNotExact($expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertNotExact($this, $expected, $pointer, $strict);

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
     * @return $this
     */
    public function assertHash(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertHash($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that the member contains a null value.
     *
     * @param string $pointer
     * @return $this
     */
    public function assertNull(string $pointer = '/data'): self
    {
        Assert::assertNull($this, $pointer);

        return $this;
    }

    /**
     * Assert that the member contains an empty list.
     *
     * @param string $pointer
     * @return $this
     */
    public function assertListEmpty(string $pointer = '/data'): self
    {
        Assert::assertListEmpty($this, $pointer);

        return $this;
    }

    /**
     * Assert that the member does not contain an empty list.
     *
     * @param string $pointer
     * @return $this
     */
    public function assertListNotEmpty(string $pointer = '/data'): self
    {
        Assert::assertListNotEmpty($this, $pointer);

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
     * @return $this
     */
    public function assertList(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertList($this, $expected, $pointer, $strict);

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
     * @return $this
     */
    public function assertExactList(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertExactList($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that a list in the document contains the hashes in the specified order.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertListInOrder(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertListInOrder($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that an array in the document contains the values in the specified order.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertExactListInOrder(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertExactListInOrder($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that the document has a list containing the expected identifier.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the array is expected in the document.
     * @return $this
     */
    public function assertListContainsIdentifier(string $type, string $id, string $pointer = '/data'): self
    {
        Assert::assertListContainsIdentifier($this, $type, $id, $pointer);

        return $this;
    }

    /**
     * Assert that a list in the document at the specified path contains the expected hash.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertListContainsHash(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertListContainsHash($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that a list in the document at the specified path contains the expected value.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertListContainsExact(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertListContainsExact($this, $expected, $pointer, $strict);

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
     * @return $this
     */
    public function assertIncluded(array $expected, bool $strict = true): self
    {
        Assert::assertIncluded($this, $expected, $strict);

        return $this;
    }

    /**
     * Assert that the expected identifier is included in the document.
     *
     * @param string $type
     * @param string $id
     * @return $this
     */
    public function assertIncludedContainsIdentifier(string $type, string $id): self
    {
        Assert::assertIncludedContainsIdentifier($this, $type, $id);

        return $this;
    }

    /**
     * Assert that the included member contains the supplied hash.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertIncludedContainsHash(array $expected, bool $strict = true): self
    {
        Assert::assertIncludedContainsHash($this, $expected, $strict);

        return $this;
    }

    /**
     * Assert that the top-level meta matches the expected values.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertMeta(array $expected, bool $strict = true): self
    {
        Assert::assertHash($this, $expected, '/meta', $strict);

        return $this;
    }

    /**
     * Assert that the top-level meta is exactly the expected meta.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertExactMeta(array $expected, bool $strict = true): self
    {
        Assert::assertExact($this, $expected, '/meta', $strict);

        return $this;
    }

    /**
     * Assert the document contains a single error that matches the supplied error.
     *
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertError(array $error, bool $strict = true): self
    {
        Assert::assertError($this, $error, $strict);

        return $this;
    }

    /**
     * Assert the document contains the supplied error within its errors member.
     *
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertHasError(array $error, bool $strict = true): self
    {
        Assert::assertHasError($this, $error, $strict);

        return $this;
    }

    /**
     * Assert the document contains the supplied errors.
     *
     * This does not assert the order of the errors, as the error order does not have any significance.
     *
     * @param array $errors
     * @param bool $strict
     * @return $this
     */
    public function assertErrors(array $errors, bool $strict = true): self
    {
        Assert::assertErrors($this, $errors, $strict);

        return $this;
    }
}
