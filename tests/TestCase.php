<?php

/**
 * Copyright 2019 Cloud Creativity Limited
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
     * @return void
     */
    protected function willFail(Closure $closure, string $message = ''): void
    {
        $didFail = false;

        try {
            $closure();
        } catch (AssertionFailedError $e) {
            $didFail = true;
        }

        $this->assertTrue($didFail, $message ?: 'Expecting test to fail.');
    }
}
