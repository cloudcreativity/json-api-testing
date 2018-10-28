<?php

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\HttpStatusIs;
use PHPUnit\Framework\Assert as PHPUnitAssert;

class HttpAssert
{

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
     * @return void
     */
    public static function assertStatus($status, int $expected, $content = null): void
    {
        PHPUnitAssert::assertThat(
            $status,
            new HttpStatusIs($expected, $content)
        );
    }

    /**
     * Assert that there is content with the expected media type.
     *
     * @param $type
     * @param $content
     * @param string $expected
     * @return Document
     */
    public static function assertContent($type, $content, $expected = self::JSON_API_MEDIA_TYPE): Document
    {
        PHPUnitAssert::assertSame($type, $expected, "Expecting content with media type {$expected}.");
        PHPUnitAssert::assertNotEmpty($content, 'Expecting HTTP body to have content.');

        return Document::cast($content);
    }

    /**
     * Assert that a resource was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertFetchedOne(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)->assertHash($expected, '/data', $strict);
    }

    /**
     * Assert that the fetched data is null.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @return Document
     */
    public static function assertFetchedNull($status, $contentType, $content): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)->assertNull();
    }

    /**
     * Assert that a resource collection was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertFetchedMany(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)->assertList($expected, '/data', $strict);
    }

    /**
     * Assert that a resource collection was fetched in the expected order.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertFetchedManyInOrder(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedEmpty($strict, $contentType, $content);
        }

        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertListInOrder($expected, '/data', $strict);
    }

    /**
     * Assert that an empty resource collection was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @return Document
     */
    public static function assertFetchedEmpty($status, $contentType, $content): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)->assertListEmpty();
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
     * @return Document
     */
    public static function assertFetchedToOne(
        $status,
        $contentType,
        $content,
        ?string $type,
        string $id = null
    ): Document
    {
        if (is_null($type) || is_null($id)) {
            return self::assertFetchedNull($status, $contentType, $content);
        }

        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertExact(compact('type', 'id'));
    }

    /**
     * Assert that a to-many relationship was fetched.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertFetchedToMany(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedEmpty($strict, $contentType, $content);
        }

        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertExactList($expected, '/data', $strict);
    }

    /**
     * Assert that a to-many relationship was fetched in the expected order.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertFetchedToManyInOrder(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        if (empty($expected)) {
            return self::assertFetchedEmpty($strict, $contentType, $content);
        }

        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertExactListInOrder($expected, '/data', $strict);
    }

    /**
     * Assert that a resource was created with a server generated id.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param $location
     * @param string $expectedLocation
     *      the expected location without the id.
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertCreatedWithId(
        $status,
        $contentType,
        $content,
        $location,
        string $expectedLocation,
        array $expected,
        bool $strict
    ): Document
    {
        self::assertStatus($status, self::STATUS_CREATED);
        $document = self::assertServerGeneratedId($contentType, $content, $expected, $strict);
        $id = $document->get('/data/id');

        PHPUnitAssert::assertNotEmpty($id, 'Expecting content to contain a resource id.');
        $expectedLocation = rtrim($expectedLocation, '/') . '/' . $id;
        PHPUnitAssert::assertSame($expectedLocation, $location, 'Unexpected Location header.');

        return $document;
    }

    /**
     * Assert that a resource was created with a client generated id.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param $location
     * @param string $expectedLocation
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertCreatedWithClientId(
        $status,
        $contentType,
        $content,
        $location,
        string $expectedLocation,
        array $expected,
        bool $strict = true
    ): Document
    {
        if (!isset($expected['id'])) {
            throw new \InvalidArgumentException('Expected resource hash must have an id.');
        }

        self::assertStatus($status, self::STATUS_CREATED);
        PHPUnitAssert::assertSame($expectedLocation, $location, 'Unexpected Location header.');

        return self::assertContent($contentType, $content)
            ->assertHash($expected, '/data', $strict);
    }

    /**
     * Assert that a resource was created with a no content response.
     *
     * @param $status
     * @param $location
     * @param $expectedLocation
     * @return void
     */
    public static function assertCreatedNoContent($status, $location, $expectedLocation): void
    {
        self::assertStatus($status, self::STATUS_NO_CONTENT);
        PHPUnitAssert::assertSame($expectedLocation, $location, 'Unexpected Location header.');
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
     * @return Document
     */
    public static function assertAcceptedWithId(
        $status,
        $contentType,
        $content,
        $contentLocation,
        string $expectedLocation,
        array $expected,
        bool $strict = true
    ): Document
    {
        self::assertStatus($status, self::STATUS_ACCEPTED);
        $document = self::assertServerGeneratedId($contentType, $content, $expected, $strict);
        $id = $document->get('/data/id');

        PHPUnitAssert::assertNotEmpty($id, 'Expecting content to contain a resource id.');
        $expectedLocation = rtrim($expectedLocation, '/') . '/' . $id;
        PHPUnitAssert::assertSame($expectedLocation, $contentLocation, 'Unexpected Location header.');

        return $document;
    }

    /**
     * Assert a no content response.
     *
     * @param $status
     * @return void
     */
    public static function assertNoContent($status): void
    {
        self::assertStatus($status, self::STATUS_NO_CONTENT);
    }

    /**
     * Assert a top-level meta response without data.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertMetaWithoutData(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertNotExists('/data')
            ->assertHash($expected, '/meta', $strict);
    }

    /**
     * Assert an exact top-level meta response without data.
     *
     * @param $status
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    public static function assertExactMetaWithoutData(
        $status,
        $contentType,
        $content,
        array $expected,
        bool $strict = true
    ): Document
    {
        self::assertStatus($status, self::STATUS_OK);

        return self::assertContent($contentType, $content)
            ->assertNotExists('/data')
            ->assertExact($expected, '/meta', $strict);
    }

    /**
     * @param $contentType
     * @param $content
     * @param array $expected
     * @param bool $strict
     * @return Document
     */
    private static function assertServerGeneratedId($contentType, $content, array $expected, bool $strict): Document
    {
        if (array_key_exists('id', $expected)) {
            throw new \InvalidArgumentException('Expected resource hash must not have an id.');
        }

        return self::assertContent($contentType, $content)
            ->assertHash($expected, '/data', $strict)
            ->assertExists('/data/id');
    }
}
