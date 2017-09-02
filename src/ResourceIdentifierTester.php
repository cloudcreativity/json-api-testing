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
 * Class ResourceIdentifierTester
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class ResourceIdentifierTester extends ObjectTester
{

    /**
     * ResourceIdentifierTester constructor.
     *
     * @param stdClass $object
     * @param int|null $index
     */
    public function __construct(stdClass $object, $index = null)
    {
        parent::__construct($object, $index);
        $this->assertComplete();
    }

    /**
     * Assert that the resource identifier matches the expected type and id.
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

        $message = $message ?: "Resource identifier [$actual] does not match expected identifier [$expected]";

        Assert::assertEquals($expected, $actual, $this->withIndex($message));

        return $this;
    }

    /**
     * @return void
     */
    private function assertComplete()
    {
        $this->assertHasType();
        $this->assertHasId();
    }
}
