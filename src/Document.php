<?php

namespace CloudCreativity\JsonApi\Testing;

use ArrayAccess;
use CloudCreativity\JsonApi\Testing\Concerns\HasDocumentAssertions;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;

class Document implements Arrayable, JsonSerializable, ArrayAccess
{

    use HasDocumentAssertions;

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

}
