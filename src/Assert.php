<?php

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\ExactInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInDocument;
use PHPUnit\Framework\Assert as PHPUnitAssert;

class Assert
{

    /**
     * Assert that the data member contains a null value.
     *
     * @param $document
     * @return void
     */
    public static function assertDataIsNull($document): void
    {
        self::assertExactData($document, null);
    }

    /**
     * Assert that the JSON API document has an array subset in the data member.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param array $expected
     *      the expected resource object.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return void
     */
    public static function assertData(
        $document,
        array $expected,
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInDocument($expected, '/data', $strict)
        );
    }

    /**
     * Assert that the JSON API document has an exact array or null in the data member.
     *
     * @param $document
     * @param array|null $expected
     * @param bool $strict
     */
    public static function assertExactData($document, ?array $expected, bool $strict = true): void
    {
        if (is_null($expected)) {
            self::assertDocumentHasSubset($document, ['data' => null], '/', true);
        } else {
            self::assertDocumentHasExact($document, $expected, '/data', $strict);
        }
    }

    /**
     * Assert that the JSON API document has the expected resource object.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the resource object is expected in the document.
     */
    public static function assertDocumentHasResourceObject(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        $expected = compact('type', 'id');

        self::assertDocumentHasSubset($document, $expected, $pointer, true);
    }

    /**
     * Assert that the expected array subset is in the document at the specified path.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param array $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return void
     */
    public static function assertDocumentHasSubset(
        $document,
        array $expected,
        string $pointer = '/',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInDocument($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that the expected array is in the document at the specified path.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param array $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return void
     */
    public static function assertDocumentHasExact(
        $document,
        array $expected,
        string $pointer = '/',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new ExactInDocument($expected, $pointer, $strict)
        );
    }
}
