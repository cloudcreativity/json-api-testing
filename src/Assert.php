<?php

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\ExactInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\NullInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\OnlyInArray;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInArray;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInDocument;
use PHPUnit\Framework\Assert as PHPUnitAssert;

class Assert
{

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
    public static function assertContains(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        $expected = compact('type', 'id');

        self::assertSubset($document, $expected, $pointer, true);
    }

    /**
     * Assert that the expected array is in the document at the specified path.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param array|null $expected
     *      the expected resource object.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return void
     */
    public static function assertExact(
        $document,
        ?array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        if (is_null($expected)) {
            self::assertNull($document, $pointer);
        } else {
            PHPUnitAssert::assertThat(
                $document,
                new ExactInDocument($expected, $pointer, $strict)
            );
        }
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
    public static function assertSubset(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInDocument($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that the member contains a null value.
     *
     * @param $document
     * @param string $pointer
     * @return void
     */
    public static function assertNull($document, string $pointer = '/data'): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new NullInDocument($pointer)
        );
    }

    /**
     * Assert that an array in the document only contains the specified subsets.
     *
     * This assertion does not check that the expected and actual arrays are in the same order.
     * To assert the order, use `assertArrayOrder`.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertArray(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new OnlyInArray($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that an array in the document contains the subsets in the specified order.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertArrayOrder(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        self::assertSubset($document, $expected, $pointer, $strict);
    }

    /**
     * Assert that the JSON API document has an array containing the expected resource object.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param string $type
     *      the expected resource object type.
     * @param string $id
     *      the expected resource object id.
     * @param string $pointer
     *      the JSON pointer to where the array is expected in the document.
     */
    public static function assertArrayContains(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        $expected = compact('type', 'id');

        self::assertArrayContainsSubset($document, $expected, $pointer, true);
    }

    /**
     * Assert that an array in the document at the specified path contains the expected subset.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     */
    public static function assertArrayContainsSubset(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInArray($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that the document's included member matches the expected array.
     *
     * This does not assert the order of the included member because there is no significance to
     * the order of resources in the included member.
     *
     * @param $document
     * @param array $expected
     * @param bool $strict
     * @return void
     */
    public static function assertIncluded($document, array $expected, bool $strict = true): void
    {
        self::assertArray($document, $expected, '/included', $strict);
    }

    /**
     * Assert that the expected resource object is included in the document.
     *
     * @param $document
     * @param string $type
     * @param string $id
     * @return void
     */
    public static function assertIncludedContains($document, string $type, string $id): void
    {
        self::assertArrayContains($document, $type, $id, '/included');
    }

    /**
     * Assert that the included member contains the supplied array subset.
     *
     * @param $document
     * @param array $expected
     * @param bool $strict
     * @return void
     */
    public static function assertIncludedContainsSubset($document, array $expected, bool $strict = true): void
    {
        self::assertArrayContainsSubset($document, $expected, '/included', $strict);
    }
}
