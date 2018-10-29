<?php

namespace CloudCreativity\JsonApi\Testing;

use ArrayAccess;
use CloudCreativity\JsonApi\Testing\Concerns\HasHttpAssertions;

/**
 * Class HttpMessage
 *
 * @package CloudCreativity\JsonApi\Testing
 * @mixin Document
 */
class HttpMessage implements ArrayAccess
{

    use HasHttpAssertions;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $contentType;

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var array
     */
    protected $headers;

    /**
     * HttpMessage constructor.
     *
     * @param int $status
     * @param string|null $contentType
     * @param string|null $content
     * @param array $headers
     */
    public function __construct(
        int $status,
        string $contentType = null,
        string $content = null,
        array $headers = []
    ) {
        $this->status = $status;
        $this->contentType = $contentType;
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $document = $this->getDocument();

        return $document->{$name}(...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->getDocument()->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->getDocument()->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        return $this->getDocument()->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        return $this->getDocument()->offsetUnset($offset);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getContentLocation(): ?string
    {
        return $this->headers['Content-Location'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->headers['Location'] ?? null;
    }

}
