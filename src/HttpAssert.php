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

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\HttpStatusIs;
use PHPUnit\Framework\Assert as PHPUnitAssert;

/**
 * Class HttpAssert
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class HttpAssert
{

    private const JSON_MEDIA_TYPE = 'application/json';
    private const JSON_API_MEDIA_TYPE = 'application/vnd.api+json';
    private const STATUS_OK = 200;
    private const STATUS_CREATED = 201;
    private const STATUS_ACCEPTED = 202;
    private const STATUS_NO_CONTENT = 204;

    /**
     * Assert that the HTTP status matches the expected status.
     *
     * This assertion will print any JSON API errors in the supplied content if
     * the HTTP status does not match. This is useful for understanding why the status
     * is not the expected status when an error has occurred.
     *
     * @param $status
     * @param int $expected
     * @param mixed|null $content
     * @param string $message
     * @return void
     */
    public static function assertStatusCode(
        $status,
        int $expected,
        $content = null,
        string $message = ''
    ): void
    {
        PHPUnitAssert::assertThat(
            $status,
            new HttpStatusIs($expected, $content),
            $message
        );
    }

    /**
     * Assert that there is content with the expected media type.
     *
     * @param $type
     * @param $content
     * @param string $expected
     * @param string $message
     * @return Document
     */
    public static function assertContent(
        $type,
        $content,
        $expected = self::JSON_API_MEDIA_TYPE,
        string $message = ''
    ): Document
    {
        PHPUnitAssert::assertSame($expected, $type, $message ?: "Expecting content with media type {$expected}.");
        PHPUnitAssert::assertNotEmpty($content, $message ?: 'Expecting HTTP body to have content.');

        return Document::cast($content);
    }

    /**
     * Assert a JSON HTTP message with an expected status.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expected
     *      the expected HTTP status.
     * @param string $message
     * @return Document
     */
    public static function assertJson(
        $status,
        $contentType,
        $content,
        int $expected = self::STATUS_OK,
        string $message = ''
    ): Document
    {
        self::assertStatusCode($status, $expected, $content, $message);

        return self::assertContent($contentType, $content, self::JSON_MEDIA_TYPE, $message);
    }

    /**
     * Assert a JSON API HTTP message with an expected status.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expected
     *      the expected HTTP status.
     * @param string $message
     * @return Document
     */
    public static function assertJsonApi(
        $status,
        $contentType,
        $content,
        int $expected = self::STATUS_OK,
        string $message = ''
    ): Document
    {
        self::assertStatusCode($status, $expected, $content, $message);

        return self::assertContent($contentType, $content, self::JSON_API_MEDIA_TYPE, $message);
    }

    /**
     * Assert that a resource was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     *      the expected resource, or a subset of the expected resource.
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedOne(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertHash($expected, '/data', $strict, $message);
    }

    /**
     * Assert that an exact resource or resource collection was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     *      the expected content of the document's data member.
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedExact(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertExact($expected, '/data', $strict, $message);
    }

    /**
     * Assert that the fetched data is null.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param string $message
     * @return Document
     */
    public static function assertFetchedNull($status, $contentType, $content, string $message = ''): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertNull('/data', $message);
    }

    /**
     * Assert that a resource collection was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedMany(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertList($expected, '/data', $strict, $message);
    }

    /**
     * Assert that a resource collection was fetched in the expected order.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedManyInOrder(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedNone($strict, $contentType, $content, $message);
        }

        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertListInOrder($expected, '/data', $strict, $message);
    }

    /**
     * Assert that an empty resource collection was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param string $message
     * @return Document
     */
    public static function assertFetchedNone($status, $contentType, $content, string $message = ''): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertListEmpty('/data', $message);
    }

    /**
     * Assert that a to-one relationship was fetched.
     *
     * If either type or id are null, then it will be asserted that the data member of the content
     * is null.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param string|null $type
     * @param string|null $id
     * @param string $message
     * @return Document
     */
    public static function assertFetchedToOne(
        $status,
        $contentType,
        $content,
        ?string $type,
        string $id = null,
        string $message = ''
    ): Document
    {
        if (is_null($type) || is_null($id)) {
            return self::assertFetchedNull($status, $contentType, $content, $message);
        }

        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertIdentifier($type, $id, '/data', $message);
    }

    /**
     * Assert that a to-many relationship was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedToMany(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedNone($strict, $contentType, $content, $message);
        }

        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertIdentifiersList($expected, '/data', $strict, $message);
    }

    /**
     * Assert that a to-many relationship was fetched in the expected order.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertFetchedToManyInOrder(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedNone($strict, $contentType, $content, $message);
        }

        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertIdentifiersListInOrder($expected, '/data', $strict, $message);
    }

    /**
     * Assert that a resource was created with a server generated id.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param $location
     * @param string|null $expectedLocation
     *      the expected location without the id, or null if none is expected.
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertCreatedWithServerId(
        $status,
        $contentType,
        $content,
        $location,
        ?string $expectedLocation,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        self::assertStatusCode($status, self::STATUS_CREATED, $content, $message);
        $document = self::assertServerGeneratedId($contentType, $content, $expected, $strict, $message);
        $id = $document->get('/data/id');

        PHPUnitAssert::assertNotEmpty($id, $message ?: 'Expecting content to contain a resource id.');

        if (null === $expectedLocation) {
            PHPUnitAssert::assertNull($location, 'Expecting no location header.');
        } else {
            $expectedLocation = rtrim($expectedLocation, '/') . '/' . $id;
            PHPUnitAssert::assertSame($expectedLocation, $location, $message ?: 'Unexpected Location header.');
        }

        return $document;
    }

    /**
     * Assert that a resource was created with a client generated id.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param $location
     * @param string|null $expectedLocation
     *      the expected location without the id, or null if none is expected.
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertCreatedWithClientId(
        $status,
        $contentType,
        $content,
        $location,
        ?string $expectedLocation,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        $expectedId = $expected['id'] ?? null;

        if (!$expectedId) {
            throw new \InvalidArgumentException('Expected resource hash must have an id.');
        }

        self::assertStatusCode($status, self::STATUS_CREATED, $content, $message);

        if (null === $expectedLocation) {
            PHPUnitAssert::assertNull($location, 'Expecting no location header.');
        } else {
            PHPUnitAssert::assertSame(
                "$expectedLocation/{$expectedId}",
                $location,
                $message ?: 'Unexpected Location header.'
            );
        }

        return self::assertContent($contentType, $content, self::JSON_API_MEDIA_TYPE, $message)
            ->assertHash($expected, '/data', $strict, $message);
    }

    /**
     * Assert that a resource was created with a no content response.
     *
     * @param $status
     * @param $location
     * @param $expectedLocation
     * @param string $message = ''
     * @return void
     */
    public static function assertCreatedNoContent(
        $status,
        $location,
        $expectedLocation,
        string $message = ''
    ): void
    {
        self::assertStatusCode($status, self::STATUS_NO_CONTENT, null, $message);
        PHPUnitAssert::assertSame($expectedLocation, $location, $message ?: 'Unexpected Location header.');
    }

    /**
     * Assert that an asynchronous process was accepted with a server id.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param $contentLocation
     * @param string $expectedLocation
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertAcceptedWithId(
        $status,
        $contentType,
        $content,
        $contentLocation,
        string $expectedLocation,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        self::assertStatusCode($status, self::STATUS_ACCEPTED, $content, $message);
        $document = self::assertServerGeneratedId($contentType, $content, $expected, $strict, $message);
        $id = $document->get('/data/id');

        PHPUnitAssert::assertNotEmpty($id, $message ?: 'Expecting content to contain a resource id.');
        $expectedLocation = rtrim($expectedLocation, '/') . '/' . $id;
        PHPUnitAssert::assertSame($expectedLocation, $contentLocation, $message ?: 'Unexpected Location header.');

        return $document;
    }

    /**
     * Assert a no content response.
     *
     * @param $status
     * @param string|null
     * @param string $message
     * @return void
     */
    public static function assertNoContent($status, string $content = null, string $message = ''): void
    {
        self::assertStatusCode($status, self::STATUS_NO_CONTENT, $content, $message);
        PHPUnitAssert::assertEmpty($content, $message ?: 'Expecting HTTP body content to be empty.');
    }

    /**
     * Assert a top-level meta response without data.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertMetaWithoutData(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertNotExists('/data', $message)
            ->assertMeta($expected, $strict, $message);
    }

    /**
     * Assert an exact top-level meta response without data.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertExactMetaWithoutData(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, self::STATUS_OK, $message)
            ->assertNotExists('/data', $message)
            ->assertExactMeta($expected, $strict, $message);
    }

    /**
     * Assert the document contains a single error that matches the supplied error.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertError(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $error = [],
        bool $strict = true,
        string $message = ''
    ): Document
    {
        $document = self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertNotExists('/data', $message);

        if ($error) {
            $document->assertError($error, $strict, $message);
        } else {
            $document->assertExists('/error', $message);
        }

        return $document;
    }

    /**
     * Assert the document contains an exact single error that matches the supplied error.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertExactError(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $error,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        $document = self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertNotExists('/data', $message)
            ->assertExactError($error, $strict, $message);

        return $document;
    }

    /**
     * Assert the document contains a single error that matches the supplied error and has a status member.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertErrorStatus(
        $status,
        $contentType,
        $content,
        array $error = [],
        bool $strict = true,
        string $message = ''
    ): Document
    {
        $expectedStatus = $error['status'] ?? null;

        if (!$expectedStatus) {
            throw new \InvalidArgumentException('Expecting error to have a status member.');
        }

        return self::assertError(
            $status,
            $contentType,
            $content,
            (int) $expectedStatus,
            $error,
            $strict,
            $message
        );
    }

    /**
     * Assert the document contains an exact single error that matches the supplied error and has a status member.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertExactErrorStatus(
        $status,
        $contentType,
        $content,
        array $error,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        $expectedStatus = $error['status'] ?? null;

        if (!$expectedStatus) {
            throw new \InvalidArgumentException('Expecting error to have a status member.');
        }

        return self::assertExactError(
            $status,
            $contentType,
            $content,
            (int) $expectedStatus,
            $error,
            $strict,
            $message
        );
    }

    /**
     * Assert the HTTP message contains the supplied error within its errors member.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertHasError(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $error = [],
        bool $strict = true,
        string $message = ''
    ): Document
    {
        if (empty($error)) {
            $error = ['status' => (string) $status];
        }

        return self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertHasError($error, $strict, $message);
    }

    /**
     * Assert the HTTP message contains the supplied error within its errors member.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $error
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertHasExactError(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $error,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        if (empty($error)) {
            $error = ['status' => (string) $status];
        }

        return self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertHasExactError($error, $strict, $message);
    }

    /**
     * Assert the HTTP status contains the supplied errors.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $errors
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertErrors(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $errors,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertErrors($errors, $strict, $message);
    }

    /**
     * Assert the HTTP status contains the exact supplied errors.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param int $expectedStatus
     * @param array $errors
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    public static function assertExactErrors(
        $status,
        $contentType,
        $content,
        int $expectedStatus,
        array $errors,
        bool $strict = true,
        string $message = ''
    ): Document
    {
        return self::assertJsonApi($status, $contentType, $content, $expectedStatus, $message)
            ->assertExactErrors($errors, $strict, $message);
    }

    /**
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @param string $message
     * @return Document
     */
    private static function assertServerGeneratedId(
        $contentType,
        $content,
        array $expected,
        bool $strict,
        string $message = ''
    ): Document
    {
        $id = $expected['id'] ?? null;

        $document = self::assertContent($contentType, $content, self::JSON_API_MEDIA_TYPE, $message)
            ->assertHash($expected, '/data', $strict, $message);

        if ($id) {
            PHPUnitAssert::assertSame($id, $document->get('/data/id'), $message);
        } else {
            $document->assertExists('/data/id', $message);
        }

        return $document;
    }
}
