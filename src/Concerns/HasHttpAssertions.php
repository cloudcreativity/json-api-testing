<?php

namespace CloudCreativity\JsonApi\Testing\Concerns;

use CloudCreativity\JsonApi\Testing\Document;
use CloudCreativity\JsonApi\Testing\HttpAssert;
use Illuminate\Contracts\Routing\UrlRoutable;

trait HasHttpAssertions
{

    /**
     * @var Document|null
     */
    protected $document;

    /**
     * @var string|null
     */
    private $expectedType;

    /**
     * Get the JSON API document.
     *
     * @return Document
     */
    public function getDocument(): Document
    {
        if (!$this->document) {
            $this->document = HttpAssert::assertContent(
                $this->getContentType(),
                $this->getContent()
            );
        }

        return $this->document;
    }

    /**
     * @return string
     */
    public function getExpectedType(): string
    {
        if (!$this->expectedType) {
            throw new \LogicException('An expected resource type must be set.');
        }

        return $this->expectedType;
    }

    /**
     * Set the expected resource type.
     *
     * @param string $type
     * @return HasHttpAssertions
     */
    public function expectsType(string $type): self
    {
        if (empty($type)) {
            throw new \InvalidArgumentException('Expected type must be a non-empty string.');
        }

        $this->expectedType = $type;

        return $this;
    }

    /**
     * @param $status
     * @return HasHttpAssertions
     */
    public function assertStatusCode($status): self
    {
        HttpAssert::assertStatusCode($this->getStatusCode(), $status, $this->getContent());

        return $this;
    }

    /**
     * Assert that a resource was fetched.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedOne($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedOne(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that the fetched data is null.
     *
     * @return $this
     */
    public function assertFetchedNull(): self
    {
        $this->document = HttpAssert::assertFetchedNull(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent()
        );

        return $this;
    }

    /**
     * Assert that a resource collection was fetched.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedMany($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedMany(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->identifiers($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource collection was fetched in the expected order.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedManyInOrder($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedManyInOrder(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->identifiers($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that an empty resource collection was fetched.
     *
     * @return $this
     */
    public function assertFetchedEmpty(): self
    {
        $this->document = HttpAssert::assertFetchedEmpty(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent()
        );

        return $this;
    }

    /**
     * Assert that a to-one relationship was fetched.
     *
     * If either type or id are null, then it will be asserted that the data member of the content
     * is null.
     *
     * @param UrlRoutable|string|null $type
     * @param string|null $id
     * @return $this
     */
    public function assertFetchedToOne($type, string $id = null): self
    {
        if ($type instanceof UrlRoutable) {
            [$type, $id] = $this->identifier($type);
        }

        $this->document = HttpAssert::assertFetchedToOne(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $type,
            $id
        );

        return $this;
    }

    /**
     * Assert that a to-many relationship was fetched.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedToMany($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedToMany(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->identifiers($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a to-many relationship was fetched in the expected order.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedToManyInOrder($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedToManyInOrder(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->identifiers($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource was created with a server generated id.
     *
     * @param $location
     * @param string $expectedLocation
     *      the expected location without the id.
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertCreatedWithId(
        $location,
        string $expectedLocation,
        $expected,
        bool $strict = true
    ): self
    {
        $this->document = HttpAssert::assertCreatedWithId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $location,
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource was created with a client generated id.
     *
     * @param $location
     * @param string $expectedLocation
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertCreatedWithClientId(
        $location,
        string $expectedLocation,
        $expected,
        bool $strict = true
    ): self
    {
        $this->document = HttpAssert::assertCreatedWithClientId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $location,
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource was created with a no content response.
     *
     * @param $location
     * @param $expectedLocation
     * @return $this
     */
    public function assertCreatedNoContent($location, $expectedLocation): self
    {
        HttpAssert::assertCreatedNoContent($this->getStatusCode(), $location, $expectedLocation);

        return $this;
    }

    /**
     * Assert that an asynchronous process was accepted with a server id.
     *
     * @param $contentLocation
     * @param string $expectedLocation
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertAcceptedWithId(
        $contentLocation,
        string $expectedLocation,
        $expected,
        bool $strict = true
    ): self
    {
        $this->document = HttpAssert::assertAcceptedWithId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $contentLocation,
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert a no content response.
     *
     * @return $this
     */
    public function assertNoContent(): self
    {
        HttpAssert::assertNoContent($this->getStatusCode());

        return $this;
    }

    /**
     * Assert that the expected identifier is included in the document.
     *
     * @param UrlRoutable|string|int|array $type
     * @param string|null $id
     * @return $this
     */
    public function assertIsIncluded($type, string $id = null): self
    {
        if (is_null($id)) {
            [$type, $id] = $this->identifier($type);
        }

        $this->getDocument()->assertIncludedContainsIdentifier($type, $id);

        return $this;
    }

    /**
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertIncludes($expected, bool $strict = true): self
    {
        $this->getDocument()->assertIncludedContainsHash(
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that the document's included member matches the expected array.
     *
     * This does not assert the order of the included member because there is no significance to
     * the order of resources in the included member.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertIncluded($expected, bool $strict = true): self
    {
        $this->getDocument()->assertIncluded(
            $this->identifiers($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert a top-level meta response without data.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertMetaWithoutData(array $expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertMetaWithoutData(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $expected,
            $strict
        );

        return $this;
    }

    /**
     * Assert an exact top-level meta response without data.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertExactMetaWithoutData(array $expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertExactMetaWithoutData(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $expected,
            $strict
        );

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
        $this->document = HttpAssert::assertError(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $error,
            $strict
        );

        return $this;
    }

    /**
     * Assert the HTTP message contains the supplied error within its errors member.
     *
     * @param int $status
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertHasError(int $status, array $error = [], bool $strict = true): self
    {
        $this->document = HttpAssert::assertHasError(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $status,
            $error,
            $strict
        );

        return $this;
    }

    /**
     * Assert the HTTP status contains the supplied errors.
     *
     * @param int $status
     * @param array $errors
     * @param bool $strict
     * @return $this
     */
    public function assertErrors(int $status, array $errors, bool $strict = true): self
    {
        $this->document = HttpAssert::assertErrors(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $status,
            $errors,
            $strict
        );

        return $this;
    }

    /**
     * Normalize ids for a find many request
     *
     * @param UrlRoutable|iterable $ids
     * @return array
     */
    protected function identifiers($ids): array
    {
        if ($ids instanceof UrlRoutable) {
            return [$this->identifier($ids)];
        }

        return collect($ids)->map(function ($id) {
            return $this->identifier($id);
        })->values()->all();
    }

    /**
     * Normalize a resource id.
     *
     * @param UrlRoutable|string|int|array $id
     * @return array
     */
    protected function identifier($id): array
    {
        if ($id instanceof UrlRoutable) {
            $id = (string) $id->getRouteKey();
        }

        if (is_string($id) || is_int($id)) {
            $id = ['id' => (string) $id];
        }

        if (!is_array($id)) {
            throw new \InvalidArgumentException('Expecting a URL routable, string, integer or array.');
        }

        /** If the type has not been specified, we will set it to the expected type. */
        if (!array_key_exists('type', $id)) {
            $id['type'] = $this->getExpectedType();
        }

        return $id;
    }
}
