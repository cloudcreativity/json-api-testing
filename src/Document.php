<?php

namespace CloudCreativity\JsonApi\Testing;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;

class Document implements Arrayable, JsonSerializable, ArrayAccess
{

    /**
     * @var array
     */
    private $document;

    /**
     * Cast a document to an instance of this document class.
     *
     * @param Document|array|string $document
     * @return Document
     */
    public static function cast($document): self
    {
        if ($document instanceof self) {
            return $document;
        }

        if (is_string($document)) {
            return self::decode($document);
        }

        return self::fromArray($document);
    }

    /**
     * @param iterable $input
     * @return Document
     */
    public static function fromArray(iterable $input): self
    {
        return new self(collect($input)->all());
    }

    /**
     * Decode a JSON string into a document.
     *
     * @param string $json
     * @return Document
     */
    public static function decode(string $json): self
    {
        $document = \json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error() || !is_array($document)) {
            throw new \InvalidArgumentException('Invalid JSON string.');
        }

        return new self($document);
    }

    /**
     * Document constructor.
     *
     * @param array $document
     */
    public function __construct(array $document)
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return collect($this->document)->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return collect($this->document)->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Not implemented.');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Not implemented.');
    }

    /**
     * Get a value from using a JSON pointer.
     *
     * @param string $pointer
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $pointer, $default = null)
    {
        if ($pointer === '/') {
            return $this->document;
        }

        return Arr::get($this->document, $this->path($pointer), $default);
    }

    /**
     * Check if an item exists using JSON pointers.
     *
     * @param string ...$pointers
     * @return bool
     */
    public function has(string ...$pointers): bool
    {
        $paths = collect($pointers)->map(function ($pointer) {
            return $this->path($pointer);
        })->all();

        return Arr::has($this->document, $paths);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->document;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return Arr::sortRecursive($this->document);
    }

    /**
     * Assert that the JSON API document has the expected resource object.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the resource object is expected in the document.
     * @return $this
     */
    public function assertContains(string $type, string $id, string $pointer = '/data'): self
    {
        Assert::assertContains($this, $type, $id, $pointer);

        return $this;
    }

    /**
     * Assert that the expected array is in the document at the specified path.
     *
     * @param array|null $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return $this
     */
    public function assertExact(?array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertExact($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that the expected array subset is in the document at the specified path.
     *
     * @param array $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return $this
     */
    public function assertSubset(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertSubset($this, $expected, $pointer, $strict);

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
     * Assert that an array in the document only contains the specified subsets.
     *
     * This assertion does not check that the expected and actual arrays are in the same order.
     * To assert the order, use `assertArrayOrder`.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertArray(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertArray($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that an array in the document contains the subsets in the specified order.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertArrayOrder(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertArrayOrder($this, $expected, $pointer, $strict);

        return $this;
    }

    /**
     * Assert that the JSON API document has an array containing the expected resource object.
     *
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the array is expected in the document.
     * @return $this
     */
    public function assertArrayContains(string $type, string $id, string $pointer = '/data'): self
    {
        Assert::assertArrayContains($this, $type, $id, $pointer);

        return $this;
    }

    /**
     * Assert that an array in the document at the specified path contains the expected subset.
     *
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return $this
     */
    public function assertArrayContainsSubset(array $expected, string $pointer = '/data', bool $strict = true): self
    {
        Assert::assertArrayContainsSubset($this, $expected, $pointer, $strict);

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
     * Assert that the expected resource object is included in the document.
     *
     * @param string $type
     * @param string $id
     * @return $this
     */
    public function assertIncludedContains(string $type, string $id): self
    {
        Assert::assertIncludedContains($this, $type, $id);

        return $this;
    }

    /**
     * Assert that the included member contains the supplied array subset.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertIncludedContainsSubset(array $expected, bool $strict = true): self
    {
        Assert::assertIncludedContainsSubset($this, $expected, $strict);

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

    /**
     * Assert the document contains the supplied error within its errors member.
     *
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertErrorsContains(array $error, bool $strict = true): self
    {
        Assert::assertErrorsContains($this, $error, $strict);

        return $this;
    }

    /**
     * @param string $pointer
     * @return string
     */
    private function path(string $pointer): string
    {
        return str_replace('/', '.', ltrim($pointer, '/'));
    }
}
