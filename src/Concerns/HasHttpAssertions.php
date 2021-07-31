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

use CloudCreativity\JsonApi\Testing\Compare;
use CloudCreativity\JsonApi\Testing\Document;
use CloudCreativity\JsonApi\Testing\HttpAssert;
use Illuminate\Contracts\Routing\UrlRoutable;

/**
 * Trait HasHttpAssertions
 *
 * @package CloudCreativity\JsonApi\Testing
 */
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
     * @param string $type
     * @return $this
     */
    public function willSeeResourceType(string $type): self
    {
        return $this->willSeeType($type);
    }

    /**
     * Set the expected resource type for the data member of the JSON document.
     *
     * @param string $type
     * @return $this
     */
    public function willSeeType(string $type): self
    {
        if (empty($type)) {
            throw new \InvalidArgumentException('Expected type must be a non-empty string.');
        }

        $this->expectedType = $type;

        return $this;
    }

    /**
     * @param $status
     * @return $this
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
     *      the expected resource, or a subset of the expected resource.
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
     * Assert that an exact resource was fetched.
     *
     * @param UrlRoutable|string|int|array $expected
     *      the expected resource.
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedOneExact($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedExact(
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
     * Assert that an exact resource collection was fetched.
     *
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertFetchedManyExact($expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertFetchedExact(
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
    public function assertFetchedNone(): self
    {
        $this->document = HttpAssert::assertFetchedNone(
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
     * Prov
     *
     * @param UrlRoutable|string|int $id
     * @return $this
     */
    public function assertFetchedToOne($id): self
    {
        $identifier = $this->identifier($id);

        $this->document = HttpAssert::assertFetchedToOne(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $identifier['type'],
            $identifier['id']
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
     * @param string|null $expectedLocation
     *      the expected location without the id, or null if no location header is expected.
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertCreatedWithServerId(?string $expectedLocation, $expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertCreatedWithServerId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->getLocation(),
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource was created with a client generated id.
     *
     * @param string|null $expectedLocation
     *      the expected location without the id, or null if no location header is expected.
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertCreatedWithClientId(?string $expectedLocation, $expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertCreatedWithClientId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->getLocation(),
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that a resource was created with a no content response.
     *
     * @param $expectedLocation
     * @return $this
     */
    public function assertCreatedNoContent(string $expectedLocation): self
    {
        HttpAssert::assertCreatedNoContent(
            $this->getStatusCode(),
            $this->getLocation(),
            $expectedLocation
        );

        return $this;
    }

    /**
     * Assert response is a JSON API resource updated response.
     *
     * For a resource update, we typically expect either:
     *
     * - 200 OK with resource content; or
     * - 204 No Content
     *
     * Alternatively a top-level meta only response is acceptable. If this is expected,
     * it can be asserted using `assertMetaWithoutData`.
     *
     * @param array $expected
     *      array representation of the expected resource, or null for a no-content response
     * @param bool $strict
     * @return $this
     * @deprecated 4.0 use not recommended: use `assertNoContent()` or `assertFetchedOne()` instead.
     */
    public function assertUpdated(array $expected = null, bool $strict = true): self
    {
        if (is_null($expected)) {
            HttpAssert::assertNoContent($this->getStatusCode(), $this->getContent());
        } else {
            $this->assertFetchedOne($expected, $strict);
        }

        return $this;
    }

    /**
     * Assert response is a JSON API resource deleted response.
     *
     * The JSON API spec says that:
     *
     * - A server MUST return a 204 No Content status code if a deletion request is successful
     * and no content is returned.
     * - A server MUST return a 200 OK status code if a deletion request is successful and the server responds
     * with only top-level meta data.
     *
     * @param array|null $expected
     *      the expected top-level meta, or null for no content response.
     * @param bool $strict
     * @return $this
     * @deprecated 4.0 use not recommended: use `assertNoContent() or `assertMetaWithoutData()` instead.
     */
    public function assertDeleted(array $expected = null, bool $strict = true): self
    {
        if (is_null($expected)) {
            HttpAssert::assertNoContent($this->getStatusCode(), $this->getContent());
        } else {
            $this->assertMetaWithoutData($expected, $strict);
        }

        return $this;
    }

    /**
     * Assert that an asynchronous process was accepted with a server id.
     *
     * @param string $expectedLocation
     * @param UrlRoutable|string|int|array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertAcceptedWithId(string $expectedLocation, $expected, bool $strict = true): self
    {
        $this->document = HttpAssert::assertAcceptedWithId(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $this->getContentLocation(),
            $expectedLocation,
            $this->identifier($expected),
            $strict
        );

        return $this;
    }

    /**
     * Assert that the expected resource is included in the document.
     *
     * @param string $type
     * @param UrlRoutable|string|int|null $id
     * @return $this
     */
    public function assertIsIncluded(string $type, $id): self
    {
        $identifier = $this->identifier(compact('type', 'id'));

        $this->getDocument()->assertIncludedContainsResource(
            $identifier['type'],
            $identifier['id']
        );

        return $this;
    }

    /**
     * Assert that the included member contains the supplied resource.
     *
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
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertIncluded(array $expected, bool $strict = true): self
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
     * Assert that the top-level meta matches the expected values.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertMeta(array $expected, bool $strict = true): self
    {
        $this->getDocument()->assertMeta($expected, $strict);

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
        $this->getDocument()->assertExactMeta($expected, $strict);

        return $this;
    }

    /**
     * Assert that the top-level links match the expected values.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertLinks(array $expected, bool $strict = true): self
    {
        $this->getDocument()->assertLinks($expected, $strict);

        return $this;
    }

    /**
     * Assert that the top-level links are exactly the expected links.
     *
     * @param array $expected
     * @param bool $strict
     * @return $this
     */
    public function assertExactLinks(array $expected, bool $strict = true): self
    {
        $this->getDocument()->assertExactLinks($expected, $strict);

        return $this;
    }

    /**
     * Assert the document contains a single error that matches the supplied error.
     *
     * @param int $status
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertError(int $status, array $error = [], bool $strict = true): self
    {
        $this->document = HttpAssert::assertError(
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
     * Assert the document contains a single  error that exactly matches the supplied error.
     *
     * @param int $status
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertExactError(int $status, array $error, bool $strict = true): self
    {
        $this->document = HttpAssert::assertExactError(
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
     * Assert the document contains a single error that matches the supplied error and has a status member.
     *
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertErrorStatus(array $error, bool $strict = true): self
    {
        $this->document = HttpAssert::assertErrorStatus(
            $this->getStatusCode(),
            $this->getContentType(),
            $this->getContent(),
            $error,
            $strict
        );

        return $this;
    }

    /**
     * Assert the document contains a single error that exactly matches the supplied error and has a status member.
     *
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertExactErrorStatus(array $error, bool $strict = true): self
    {
        $this->document = HttpAssert::assertExactErrorStatus(
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
     * Assert the HTTP message contains the exact supplied error within its errors member.
     *
     * @param int $status
     * @param array $error
     * @param bool $strict
     * @return $this
     */
    public function assertHasExactError(int $status, array $error, bool $strict = true): self
    {
        $this->document = HttpAssert::assertHasExactError(
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
     * Assert the HTTP status contains the supplied exact errors.
     *
     * @param int $status
     * @param array $errors
     * @param bool $strict
     * @return $this
     */
    public function assertExactErrors(int $status, array $errors, bool $strict = true): self
    {
        $this->document = HttpAssert::assertExactErrors(
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
     * Ensure the value is an array of identifiers.
     *
     * @param UrlRoutable|string|int|iterable $ids
     * @param string|null $type
     * @return array
     */
    protected function identifiers($ids, string $type = null): array
    {
        return Compare::identifiers($ids, $type ?: $this->getExpectedType());
    }

    /**
     * Ensure the value is a resource identifier.
     *
     * @param UrlRoutable|string|int|array $id
     * @param string|null $type
     * @return array
     */
    protected function identifier($id, string $type = null): array
    {
        return Compare::identifier($id, $type ?: $this->getExpectedType());
    }

    /**
     * Does the value identify a resource?
     *
     * @param $id
     * @return bool
     */
    protected function identifiable($id): bool
    {
        return Compare::identifiable($id);
    }
}
