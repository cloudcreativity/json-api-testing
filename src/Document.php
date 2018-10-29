<?php

namespace CloudCreativity\JsonApi\Testing;

use ArrayAccess;
use CloudCreativity\JsonApi\Testing\Concerns\HasDocumentAssertions;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;
use PHPUnit\Framework\Assert as PHPUnitAssert;

class Document implements Arrayable, JsonSerializable, ArrayAccess
{

    use HasDocumentAssertions;

    /**
     * @var array
     */
    private $document;

    /**
     * Safely create a document.
     *
     * @param $content
     * @return Document|null
     */
    public static function create($content): ?self
    {
        if (is_string($content)) {
            return self::fromString($content);
        }

        return $content ? self::cast($content) : null;
    }

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

        return self::fromIterable($document);
    }

    /**
     * Create a document from an iterable.
     *
     * @param iterable $input
     * @return Document
     */
    public static function fromIterable(iterable $input): self
    {
        return new self(collect($input)->all());
    }

    /**
     * Create a document from a string.
     *
     * @param string $json
     * @return Document|null
     */
    public static function fromString(string $json): ?self
    {
        $document = \json_decode($json, true);

        if (JSON_ERROR_NONE !== \json_last_error() || !\is_array($document)) {
            return null;
        }

        return new self($document);
    }

    /**
     * Decode a JSON string into a document.
     *
     * @param string $json
     * @return Document
     */
    public static function decode(string $json): self
    {
        if (!$document = self::fromString($json)) {
            throw new \InvalidArgumentException('Invalid JSON string.');
        }

        return $document;
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
        if (!$path = Compare::path($pointer)) {
            return $this->document;
        }

        return Arr::get($this->document, $path, $default);
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
            return Compare::path($pointer);
        })->filter()->all();

        return Arr::has($this->document, $paths);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Compare::stringify($this->document);
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
        return Compare::sort($this->document);
    }

    /**
     * Assert that all the provided members exist.
     *
     * @param string ...$pointers
     * @return $this
     */
    public function assertExists(string ...$pointers): self
    {
        $missing = collect($pointers)->reject(function ($pointer) {
            return $this->has($pointer);
        })->implode(', ');

        PHPUnitAssert::assertEmpty($missing, "Members [{$missing}] do not exist.");

        return $this;
    }

    /**
     * Assert that the provided members do not exist.
     *
     * @param string ...$pointers
     * @return Document
     */
    public function assertNotExists(string ...$pointers): self
    {
        $unexpected = collect($pointers)->filter(function ($pointer) {
            return $this->has($pointer);
        })->implode(', ');

        PHPUnitAssert::assertEmpty($unexpected, "Members [{$unexpected}] exist.");

        return $this;
    }

}
