<?php

/**
 * Copyright 2017 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing;

use CloudCreativity\Utils\Object\Obj;
use PHPUnit\Framework\Assert;
use stdClass;

/**
 * Class ResourceTester
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class ResourceObjectTester extends ObjectTester
{

    /**
     * @var stdClass
     * @deprecated
     */
    private $resource;

    /**
     * ResourceTester constructor.
     *
     * @param stdClass $resource
     * @param int|null $index
     *      if the resource appears within a collection, its index within that collection.
     */
    public function __construct(stdClass $resource, $index = null)
    {
        parent::__construct($resource, $index);
        $this->resource = $resource;
        $this->assertComplete();
    }

    /**
     * @return stdClass
     */
    public function getResource()
    {
        return clone $this->resource;
    }

    /**
     * @return int|null
     */
    public function getIndex()
    {
        return is_int($this->index) ? $this->index : null;
    }

    /**
     * @param $type
     * @param $id
     * @return bool
     */
    public function is($type, $id)
    {
        return $this->getType() === $type && $this->getId() == $id;
    }

    /**
     * Assert that the resource matches the expected structure.
     *
     * @param array $expected
     *      the expected array representation of the resource.
     * @return $this
     */
    public function assertMatches(array $expected)
    {
        if (!isset($expected[self::KEYWORD_TYPE])) {
            Assert::fail('Expected resource data must contain a type key.');
        }

        $attributes = isset($expected[self::KEYWORD_ATTRIBUTES]) ?
            $expected[self::KEYWORD_ATTRIBUTES] : [];

        $relationships = isset($expected[self::KEYWORD_RELATIONSHIPS]) ?
            $this->normalizeRelationships($expected[self::KEYWORD_RELATIONSHIPS]) : [];

        /** Have we got the correct resource id? */
        if (isset($expected[self::KEYWORD_ID])) {
            $this->assertIs($expected[self::KEYWORD_TYPE], $expected[self::KEYWORD_ID]);
        } else {
            $this->assertTypeIs($expected[self::KEYWORD_TYPE]);
        }

        /** Have we got the correct attributes? */
        $this->assertAttributesSubset($attributes);

        /** Have we got the correct relationships? */
        $this->assertRelationshipsSubset($relationships);

        return $this;
    }

    /**
     * Assert that the resource matches the expected type and id.
     *
     * @param $expectedType
     * @param $expectedId
     * @param string|null $message
     * @return $this
     */
    public function assertIs($expectedType, $expectedId, $message = null)
    {
        $actualType = isset($this->object->{self::KEYWORD_TYPE}) ? $this->object->{self::KEYWORD_TYPE} : null;
        $actualId = isset($this->object->{self::KEYWORD_ID}) ? $this->object->{self::KEYWORD_ID} : null;
        $expected = sprintf('%s:%s', $expectedType, $expectedId);
        $actual = sprintf('%s:%s', $actualType, $actualId);

        $message = $message ?: "Resource object [$actual] does not match expected resource [$expected]";

        Assert::assertEquals($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return isset($this->resource->{self::KEYWORD_TYPE}) ?
            $this->resource->{self::KEYWORD_TYPE} : null;
    }

    /**
     * Assert that the resource has a type member.
     *
     * @param string|null $message
     * @return $this
     * @deprecated use `assertHasType`
     */
    public function assertType($message = null)
    {
        return $this->assertHasType($message);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return isset($this->resource->{self::KEYWORD_ID}) ?
            $this->resource->{self::KEYWORD_ID} : null;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return isset($this->resource->{self::KEYWORD_ATTRIBUTES}) ?
            $this->resource->{self::KEYWORD_ATTRIBUTES} : null;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $attributes = $this->getAttributes();

        return is_object($attributes) && isset($attributes->{$key}) ? $attributes->{$key} : null;
    }

    /**
     * Assert that an attribute value is equal to the expected value.
     *
     * @param string $key
     * @param mixed $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttribute($key, $expected, $message = null)
    {
        $message = $message ?: "Unexpected attribute [$key]";
        $actual = $this->getAttribute($key);
        Assert::assertEquals($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that an attribute value is the same as the expected value.
     *
     * @param string $key
     * @param mixed $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttributeIs($key, $expected, $message = null)
    {
        $message = $message ?: "Unexpected attribute [$key]";
        $actual = $this->getAttribute($key);
        Assert::assertSame($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that the resource's attributes contains the provided subset.
     *
     * @param object|array $expected
     * @param string|null $message
     * @return $this
     */
    public function assertAttributesSubset($expected, $message = null)
    {
        $expected = Obj::toArray($expected);
        $actual = Obj::toArray($this->getAttributes() ?: []);
        $message = $message ?
            $this->withIndex($message) :
            $this->withIndex('Unexpected resource attributes') . ': ' . json_encode($actual);

        Assert::assertArraySubset($expected, $actual, false, $message);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelationships()
    {
        return isset($this->resource->{self::KEYWORD_RELATIONSHIPS}) ?
            $this->resource->{self::KEYWORD_RELATIONSHIPS} : null;
    }

    /**
     * Assert that the resource's relationships contains the provided subset.
     *
     * @param object|array $expected
     * @param string|null $message
     * @return $this
     */
    public function assertRelationshipsSubset($expected, $message = null)
    {
        $expected = Obj::toArray($expected);
        $actual = Obj::toArray($this->getRelationships() ?: []);
        $message = $message ?
            $this->withIndex($message) :
            $this->withIndex('Unexpected resource relationships') . ': ' . json_encode($actual);

        Assert::assertArraySubset($expected, $actual, false, $message);

        return $this;
    }

    /**
     * @return void
     */
    private function assertComplete()
    {
        $type = $this->getType();

        if (!is_string($type) || empty($type)) {
            Assert::fail($this->withIndex('Resource does not have a type member'));
        }

        $id = $this->getId();

        if (!is_string($id) && !is_int($id)) {
            Assert::fail($this->withIndex('Resource does not have an id member'));
        } elseif (is_string($id) && empty($id)) {
            Assert::fail($this->withIndex('Resource has an empty string id member'));
        }
    }

    /**
     * @param array $relationships
     * @return array
     */
    private function normalizeRelationships(array $relationships)
    {
        $normalized = [];

        foreach ($relationships as $key => $value) {

            if (is_numeric($key)) {
                $key = $value;
                $value = [];
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
