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

use PHPUnit\Framework\Assert;
use stdClass;

/**
 * Class ErrorTester
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class ErrorTester
{

    const KEYWORD_ID     = 'id';
    const KEYWORD_LINKS  = 'links';
    const KEYWORD_STATUS = 'status';
    const KEYWORD_CODE   = 'code';
    const KEYWORD_TITLE  = 'title';
    const KEYWORD_DETAIL = 'detail';
    const KEYWORD_META   = 'meta';
    const KEYWORD_ABOUT  = 'about';
    const KEYWORD_SOURCE = 'source';
    const KEYWORD_SOURCE_POINTER = 'pointer';
    const KEYWORD_SOURCE_PARAMETER = 'parameter';

    /**
     * @var stdClass
     */
    private $error;

    /**
     * @var int
     */
    private $index;

    /**
     * ErrorTester constructor.
     *
     * @param stdClass $error
     * @param int $index
     *      the index within the error collection at which this error exists.
     */
    public function __construct(stdClass $error, $index = 0)
    {
        $this->error = $error;
        $this->index = $index;
    }

    /**
     * @return stdClass
     */
    public function getError()
    {
        return clone $this->error;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return isset($this->error->{self::KEYWORD_CODE}) ?
            $this->error->{self::KEYWORD_CODE} : null;
    }

    /**
     * Assert that the error code equals the expected code.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertCode($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid code at error index %d', $this->index);
        Assert::assertEquals($expected, $this->getCode(), $message);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return isset($this->error->{self::KEYWORD_STATUS}) ?
            $this->error->{self::KEYWORD_STATUS} : null;
    }

    /**
     * Assert that the error status equals the expected status.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertStatus($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid status at error index %d', $this->index);
        Assert::assertEquals($expected, $this->getStatus(), $message);

        return $this;
    }

    /**
     * @return stdClass|null
     */
    public function getSource()
    {
        $source = isset($this->error->{self::KEYWORD_SOURCE}) ?
            $this->error->{self::KEYWORD_SOURCE} : null;

        if (!is_null($source) && !$source instanceof stdClass) {
            Assert::fail(sprintf('Invalid error source at index %d', $this->index));
        }

        return $source;
    }

    /**
     * @return mixed
     */
    public function getSourcePointer()
    {
        $source = $this->getSource() ?: new stdClass();

        return isset($source->{self::KEYWORD_SOURCE_POINTER}) ? $source->{self::KEYWORD_SOURCE_POINTER} : null;
    }

    /**
     * @return mixed
     */
    public function getSourceParameter()
    {
        $source = $this->getSource() ?: new stdClass();

        return isset($source->{self::KEYWORD_SOURCE_PARAMETER}) ? $source->{self::KEYWORD_SOURCE_PARAMETER} : null;
    }

    /**
     * Assert that the error source pointer equals the expected pointer.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertPointer($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid source pointer at error index %d', $this->index);
        Assert::assertEquals($expected, $this->getSourcePointer(), $message);

        return $this;
    }

    /**
     * Assert that the error source parameter equals the expected parameter.
     *
     * @param $expected
     * @param string|null $message
     * @return $this
     */
    public function assertParameter($expected, $message = null)
    {
        $message = $message ?: sprintf('Invalid source parameter at error index %d', $this->index);
        Assert::assertEquals($expected, $this->getSourceParameter(), $message);

        return $this;
    }
}
