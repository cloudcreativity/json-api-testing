<?php

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\JsonApi\Testing\Constraints\ExactInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\ExactInList;
use CloudCreativity\JsonApi\Testing\Constraints\OnlyExactInList;
use CloudCreativity\JsonApi\Testing\Constraints\OnlySubsetsInList;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInDocument;
use CloudCreativity\JsonApi\Testing\Constraints\SubsetInList;
use PHPUnit\Framework\Assert as PHPUnitAssert;
use PHPUnit\Framework\Constraint\LogicalNot;

class Assert
{

    /**
     * Assert that the value at the pointer has the expected JSON API identifier.
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
    public static function assertIdentifier(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        self::assertHash($document, compact('type', 'id'), $pointer, true);
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
     * Assert that the expected hash is in the document at the specified path.
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
    public static function assertHash(
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
    public static function assertListEmpty($document, string $pointer = '/data'): void
    {
        self::assertExactList($document, [], $pointer, true);
    }

    /**
     * Assert that the member does not contain an empty list.
     *
     * @param $document
     * @param string $pointer
     * @return void
     */
    public static function assertListNotEmpty($document, string $pointer = '/data'): void
    {
        self::assertNotExact($document, [], $pointer, true);
    }

    /**
     * Assert that a list in the document only contains the specified hashes.
     *
     * This assertion does not check that the expected and actual lists are in the same order.
     * To assert the order, use `assertListInOrder`.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertList(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new OnlySubsetsInList($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that a list in the document only contains the specified values.
     *
     * This assertion does not check that the expected and actual lists are in the same order.
     * To assert the order, use `assertExactListInOrder`.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertExactList(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new OnlyExactInList($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that a list in the document contains the hashes in the specified order.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertListInOrder(
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
     * Assert that a list in the document contains the values in the specified order.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertExactListInOrder(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        self::assertExact($document, $expected, $pointer, $strict);
    }

    /**
     * Assert that the document has a list containing the expected identifier.
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
    public static function assertListContainsIdentifier(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        $expected = compact('type', 'id');

        self::assertListContainsHash($document, $expected, $pointer, true);
    }

    /**
     * Assert that a list in the document at the specified path contains the expected hash.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     */
    public static function assertListContainsHash(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new SubsetInList($expected, $pointer, $strict)
        );
    }

    /**
     * Assert that a list in the document at the specified path contains the expected value.
     *
     * @param $document
     * @param array $expected
     * @param string $pointer
     * @param bool $strict
     * @return void
     */
    public static function assertListContainsExact(
        $document,
        array $expected,
        string $pointer = '/data',
        bool $strict = true
    ): void
    {
        PHPUnitAssert::assertThat(
            $document,
            new ExactInList($expected, $pointer, $strict)
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
        self::assertList($document, $expected, '/included', $strict);
    }

    /**
     * Assert that the expected identifier is included in the document.
     *
     * @param $document
     * @param string $type
     * @param string $id
     * @return void
     */
    public static function assertIncludedContainsIdentifier($document, string $type, string $id): void
    {
        self::assertListContainsIdentifier($document, $type, $id, '/included');
    }

    /**
     * Assert that the included member contains the supplied hash.
     *
     * @param $document
     * @param array $expected
     * @param bool $strict
     * @return void
     */
    public static function assertIncludedContainsHash($document, array $expected, bool $strict = true): void
    {
        self::assertListContainsHash($document, $expected, '/included', $strict);
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
        self::assertList($document, [$error], '/errors', $strict);
    }

    /**
     * Assert the document contains the supplied error within its errors member.
     *
     * @param $document
     * @param array $error
     * @param bool $strict
     * @return void
     */
    public static function assertHasError($document, array $error, bool $strict = true): void
    {
        self::assertListContainsHash($document, $error, '/errors', $strict);
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
        self::assertList($document, $errors, '/errors', $strict);
    }
}
