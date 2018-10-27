<?php

namespace CloudCreativity\JsonApi\Testing;

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
    public static function assertHasResourceObject(
        $document,
        string $type,
        string $id,
        string $pointer = '/data'
    ): void
    {
        $expected = compact('type', 'id');

        self::assertResourceObjectSubset($document, $expected, $pointer, true);
    }

    /**
     * Assert that the JSON API document has a matching resource object.
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
    public static function assertResourceObjectSubset(
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
}
