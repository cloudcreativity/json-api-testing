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

use Closure;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class TestCase extends BaseTestCase
{

    /**
     * @param Closure $closure
     * @param string $message
     */
    protected function willFail(Closure $closure, $message = '')
    {
        $didFail = false;

        try {
            $closure();
        } catch (AssertionFailedError $e) {
            $didFail = true;
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            /** @todo remove this catch block when dropping support for PHPUnit 5.7 */
            $didFail = true;
        }

        $this->assertTrue($didFail, $message ?: 'Expecting test to fail.');
    }
}
