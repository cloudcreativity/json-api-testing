<?php
/*
 * Copyright 2022 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SebastianBergmann\Comparator\ComparisonFailure;
use function array_replace_recursive;
use function is_array;

/**
 * Class Compare
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class Compare
{

    /**
     * Does the actual value match the expected value?
     *
     * @param array|null $expected
     * @param mixed $actual
     * @param bool $strict
     * @return bool
     */
    public static function exact(?array $expected, $actual, bool $strict = true): bool
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
     * @param mixed $actual
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
     * Is the supplied value a resource identifier?
     *
     * A resource identifier must have a type and id member. It must not have attributes
     * or relationship members, otherwise it is a resource object.
     *
     * - attributes
     * - relationships
     *
     * @param mixed $value
     * @return bool
     */
    public static function resourceIdentifier($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $members = Collection::make($value);

        return $members->has('type') &&
            $members->has('id') &&
            !$members->has('attributes') &&
            !$members->has('relationships') &&
            !$members->has('links');
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
        return array_replace_recursive($value, $patch);
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
     * @param mixed $value
     * @return mixed
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

        return Collection::make($value)->map(function ($item) {
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
     * @param mixed $expected
     * @param mixed $actual
     * @param bool $subset
     *      whether the expected is meant to be a subset of the actual.
     * @return ComparisonFailure
     */
    public static function failure($expected, $actual, bool $subset = false): ComparisonFailure
    {
        if ($subset && is_array($actual)) {
            $expected = self::patch($actual, $expected);
        }

        $expected = self::normalize($expected);
        $actual = self::normalize($actual);

        return new ComparisonFailure(
            $expected,
            $actual,
            self::stringify($expected),
            self::stringify($actual)
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
