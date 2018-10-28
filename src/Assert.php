<?php

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\ExactInArray;
use CloudCreativity\JsonApi\Testing\Constraints\ExactInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\OnlyExactInArray;
use CloudCreativity\JsonApi\Testing\Constraints\OnlySubsetsInArray;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInArray;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInDocument;
use PHPUnit\Framework\Assert as PHPUnitAssert;
use PHPUnit\Framework\Constraint\LogicalNot;

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
     * Assert that the expected value is in the document at the specified path.
     *
     * @param array|string $document
     *      the JSON API document.
     * @param mixed $expected
     *      the expected value.
     * @param string $pointer
     *      the JSON pointer to where the object is expected to exist within the document.
     * @param bool $strict
     *      whether strict comparison should be used.
     * @return void
     */
    public static function assertExact(
        $document,
        $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new ExactInDocument($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that the value at the specified path is not the expected value.
     *
     * @param $document
     * @param $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertNotExact($document, $expected, string $pointer = '/data', bool $strict = true): void
    {
        $constraint = new LogicalNot(
            new ExactInDocument($expected, $pointer, $strict)
        );

        PHPUnitAssert::assertThat($document, $constraint);
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
        self::assertExact($document, null, $pointer, true);
    }

    /**
     * Assert that the member contains an empty list.
     *
     * @param $document
     * @param string $pointer
     * @return void
     */
    public static function assertArrayEmpty($document, string $pointer = '/data'): void
    {
        self::assertExact($document, [], $pointer, true);
    }

    /**
     * Assert that the member does not contain an empty list.
     *
     * @param $document
     * @param string $pointer
     * @return void
     */
    public static function assertArrayNotEmpty($document, string $pointer = '/data'): void
    {
        self::assertNotExact($document, [], $pointer, true);
    }

    /**
     * Assert that an array in the document only contains the specified subsets.
     *
     * This assertion does not check that the expected and actual arrays are in the same order.
     * To assert the order, use `assertArrayInOrder`.
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
            new OnlySubsetsInArray($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that an array in the document only contains the specified values.
     *
     * This assertion does not check that the expected and actual arrays are in the same order.
     * To assert the order, use `assertExactArrayInOrder`.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertExactArray(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new OnlyExactInArray($expected, $pointer, $strict)
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
    public static function assertArrayInOrder(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        self::assertSubset($document, $expected, $pointer, $strict);
    }

    /**
     * Assert that an array in the document contains the values in the specified order.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertExactArrayInOrder(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        self::assertExact($document, $expected, $pointer, $strict);
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
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInArray($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that an array in the document at the specified path contains the expected value.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertArrayContainsExact(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new ExactInArray($expected, $pointer, $strict)
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

    /**
     * Assert the document contains a single error that matches the supplied error.
     *
     * @param $document
     * @param array $error
     * @param bool $strict
     * @return void
     */
    public static function assertError($document, array $error, bool $strict = true): void
    {
        self::assertArray($document, [$error], '/errors', $strict);
    }

    /**
     * Assert the document contains the supplied errors.
     *
     * This does not assert the order of the errors, as the error order does not have any significance.
     *
     * @param $document
     * @param array $errors
     * @param bool $strict
     * @return void
     */
    public static function assertErrors($document, array $errors, bool $strict = true): void
    {
        self::assertArray($document, $errors, '/errors', $strict);
    }

    /**
     * Assert the document contains the supplied error within its errors member.
     *
     * @param $document
     * @param array $error
     * @param bool $strict
     * @return void
     */
    public static function assertErrorsContains($document, array $error, bool $strict = true): void
    {
        self::assertArrayContainsSubset($document, $error, '/errors', $strict);
    }
}
