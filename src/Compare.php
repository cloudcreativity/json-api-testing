<?php

namespace CloudCreativity\JsonApi\Testing;

use Illuminate\Support\Arr;
use SebastianBergmann\Comparator\ComparisonFailure;

class Compare
{

    /**
     * Does the actual value match the expected value?
     *
     * @param array $expected
     * @param $actual
     * @param bool $strict
     * @return bool
     */
    public static function exact($expected, $actual, bool $strict = true): bool
    {
        $expected = self::normalize($expected);
        $actual = self::normalize($actual);

        if ($strict) {
            return $expected === $actual;
        }

        return $expected == $actual;
    }

    /**
     * Does the expected subset appear within the actual value?
     *
     * @param array $expected
     * @param $actual
     * @param bool $strict
     * @return bool
     */
    public static function subset(array $expected, $actual, bool $strict = true): bool
    {
        if (!is_array($actual)) {
            return false;
        }

        $patched = self::patch($actual, $expected);

        return self::exact($patched, $actual, $strict);
    }

    /**
     * Apply a patch to a value.
     *
     * @param array $value
     * @param array $patch
     * @return array
     */
    public static function patch(array $value, array $patch): array
    {
        return \array_replace_recursive($value, $patch);
    }

    /**
     * Is the value a JSON hash?
     *
     * @param $value
     * @return bool
     */
    public static function hash($value): bool
    {
        return is_array($value) && Arr::isAssoc($value);
    }

    /**
     * Normalize a value for comparison.
     *
     * @param $value
     * @return array
     */
    public static function normalize($value)
    {
        return is_array($value) ? self::sort($value) : $value;
    }

    /**
     * Recursively sort a JSON hash or JSON list for comparison.
     *
     * @param array $value
     * @return array
     */
    public static function sort(array $value): array
    {
        if (self::hash($value)) {
            ksort($value);
        }

        return collect($value)->map(function ($item) {
            return self::normalize($item);
        })->all();
    }

    /**
     * Stringify a value.
     *
     * @param mixed $value
     * @return string
     */
    public static function stringify($value): string
    {
        return json_encode(self::normalize($value), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $expected
     * @param $actual
     * @param bool $subset
     * @return ComparisonFailure
     */
    public static function failure($expected, $actual, bool $subset = false): ComparisonFailure
    {
        if ($subset && is_array($actual)) {
            $expected = self::patch($expected, $actual);
        }

        $expected = self::normalize($expected);
        $actual = self::normalize($actual);

        return new ComparisonFailure(
            $expected,
            $actual,
            \var_export($expected, true),
            \var_export($actual, true)
        );
    }

    /**
     * Convert a JSON pointer to a dot notation path.
     *
     * @param string $pointer
     * @return string
     */
    public static function path(string $pointer): string
    {
        if ('/' === $pointer) {
            return '';
        }

        return str_replace('/', '.', ltrim($pointer, '/'));
    }
}
