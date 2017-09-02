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

use stdClass;
use PHPUnit\Framework\Assert;

/**
 * Class ObjectTester
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class ObjectTester
{

    const KEYWORD_DATA = 'data';
    const KEYWORD_INCLUDED = 'included';
    const KEYWORD_ERRORS = 'errors';
    const KEYWORD_TYPE = 'type';
    const KEYWORD_ID = 'id';
    const KEYWORD_ATTRIBUTES = 'attributes';
    const KEYWORD_RELATIONSHIPS = 'relationships';

    /**
     * @var stdClass
     */
    public $object;

    /**
     * @var int|null
     */
    public $index;

    /**
     * ObjectTester constructor.
     *
     * @param stdClass $object
     * @param int|null $index
     *      if the object appears in a collection, its index within the collection
     */
    public function __construct(stdClass $object, $index = null)
    {
        $this->object = $object;
        $this->index = $index;
    }

    /**
     * Assert that a JSON API member exists on the object.
     *
     * @param $name
     * @param string|null $message
     * @return $this
     */
    public function assertMemberExists($name, $message = null)
    {
        $message = $this->withIndex($message ?: "JSON API object does not have expected member [$name]");
        Assert::assertObjectHasAttribute($name, $this->object, $message);

        return $this;
    }

    /**
     * Assert that a JSON API member does not exist on the object.
     *
     * @param $name
     * @param string|null $message
     * @return $this
     */
    public function assertMemberMissing($name, $message = null)
    {
        $message = $this->withIndex($message ?: "JSON API object has unexpected member [$name]");
        Assert::assertObjectNotHasAttribute($name, $this->object, $message);

        return $this;
    }

    /**
     * Assert that a member exists and is of an expected PHP type.
     *
     * @param $name
     * @param $type
     * @param string|null $message
     * @return $this
     */
    public function assertMemberInternalType($name, $type, $message = null)
    {
        $this->assertMemberExists($name, $message);
        $message = $message ?: "JSON API member $name is not expected type [$type]";
        Assert::assertInternalType($type, $this->object->{$name}, $this->withIndex($message));

        return $this;
    }

    /**
     * Assert that a member is empty.
     *
     * @param $name
     * @param null $message
     * @return $this
     */
    public function assertMemberEmpty($name, $message = null)
    {
        $actual = isset($this->object->{$name}) ? $this->object->{$name} : null;
        Assert::assertEmpty($actual, $this->withIndex($message ?: "JSON API member $name must be empty"));

        return $this;
    }

    /**
     * Assert that a member is not empty.
     *
     * @param $name
     * @param null $message
     * @return $this
     */
    public function assertMemberNotEmpty($name, $message = null)
    {
        $actual = isset($this->object->{$name}) ? $this->object->{$name} : null;
        Assert::assertNotEmpty($actual, $this->withIndex($message ?: "JSON API member $name must not be empty"));

        return $this;
    }

    /**
     * Assert that the object has a data member.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertHasData($message = null)
    {
        $this->assertMemberExists(self::KEYWORD_DATA, $message);

        return $this;
    }

    /**
     * Assert that the object has a type member.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertHasType($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_TYPE, 'string', $message);
        $this->assertMemberNotEmpty(self::KEYWORD_TYPE, $message);

        return $this;
    }

    /**
     * Assert that the object type member matches the expected type(s)
     *
     * @param string|string[] $expected
     * @param string|null $message
     * @return $this
     */
    public function assertTypeIs($expected, $message = null)
    {
        $actual = isset($this->object->{self::KEYWORD_TYPE}) ? $this->object->{self::KEYWORD_TYPE} : null;
        $message = $message ?: "Unexpected JSON API object type: " . implode(',', (array) $expected);

        if (!is_array($expected)) {
            Assert::assertEquals($expected, $actual, $this->withIndex($message));
        } else {
            Assert::assertContains($actual, $expected, $this->withIndex($message));
        }

        return $this;
    }

    /**
     * Assert that the object has an id member.
     *
     * @param string|null $message
     * @return $this
     */
    public function assertHasId($message = null)
    {
        $this->assertMemberInternalType(self::KEYWORD_ID, 'string', $message);
        $this->assertMemberNotEmpty(self::KEYWORD_ID, $message);

        return $this;
    }

    /**
     * Assert that the object id member matches the expected id(s)
     *
     * @param string|string[] $expected
     * @param string|null $message
     * @return $this
     */
    public function assertIdIs($expected, $message = null)
    {
        $actual = isset($this->object->{self::KEYWORD_ID}) ? $this->object->{self::KEYWORD_ID} : null;
        $message = $message ?: sprintf("Unexpected JSON API object id [%s]", implode(',', (array) $expected));

        if (!is_array($expected)) {
            Assert::assertEquals($expected, $actual, $this->withIndex($message));
        } else {
            Assert::assertContains($actual, $expected, $this->withIndex($message));
        }

        return $this;
    }

    /**
     * @param $message
     * @return string
     */
    protected function withIndex($message = null)
    {
        if ($message && is_int($this->index)) {
            $message .= " at index [$this->index]";
        }

        return $message ?: '';
    }

}
