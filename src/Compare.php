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

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Arr;
use SebastianBergmann\Comparator\ComparisonFailure;

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
     * Is the supplied value a resource identifier?
     *
     * A resource identifier must have a type and id member. It must not have attributes
     * or relationship members, otherwise it is a resource object.
     *
     * - attributes
     * - relationships
     *
     * @param $value
     * @return bool
     */
    public static function resourceIdentifier($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $members = collect($value);

        return $members->has('type') &&
            $members->has('id') &&
            !$members->has('attributes') &&
            !$members->has('relationships');
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

    /**
     * Ensure the value is an array of identifiers.
     *
     * @param UrlRoutable|string|int|iterable $ids
     * @param string|null $type
     *      the type to use if $id does not already have a type.
     * @return array
     */
    public static function identifiers($ids, ?string $type): array
    {
        if (self::identifiable($ids)) {
            return [self::identifier($ids, $type)];
        }

        return collect($ids)->map(function ($id) use ($type) {
            return self::identifier($id, $type);
        })->values()->all();
    }

    /**
     * Ensure the value is a resource identifier.
     *
     * @param UrlRoutable|string|int|array $id
     * @param string|null $type
     *      the type to use if $id does not already have a type.
     * @return array
     */
    public static function identifier($id, ?string $type): array
    {
        if ($id instanceof UrlRoutable) {
            $id = (string) $id->getRouteKey();
        }

        if (is_string($id) || is_int($id)) {
            return ['type' => $type, 'id' => (string) $id];
        }

        if (!Compare::hash($id)) {
            throw new \InvalidArgumentException('Expecting a URL routable, string, integer or array hash.');
        }

        if (isset($id['id']) && $id['id'] instanceof UrlRoutable) {
            $id['id'] = (string) $id['id']->getRouteKey();
        }

        if ($type && !array_key_exists('type', $id)) {
            $id['type'] = $type;
        }

        return $id;
    }

    /**
     * Does the value identify a resource?
     *
     * @param $value
     * @return bool
     */
    public static function identifiable($value): bool
    {
        return $value instanceof UrlRoutable ||
            is_string($value) ||
            is_int($value) ||
            self::hash($value);
    }
}
