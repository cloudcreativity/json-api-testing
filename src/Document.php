<?php

namespace CloudCreativity\JsonApi\Testing;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Document implements Arrayable, \JsonSerializable
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
        return $this->toArray();
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
