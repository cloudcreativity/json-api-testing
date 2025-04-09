<?php

/*
 * Copyright 2025 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

namespace CloudCreativity\JsonApi\Testing\Tests;

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
     * @param string $expected
     * @param Closure $closure
     * @param string $message
     * @return void
     */
    protected function assertThatItFails(string $expected, Closure $closure, string $message = ''): void
    {
        $actual = null;

        try {
            $closure();
        } catch (AssertionFailedError $e) {
            $actual = $e;
        }

        $this->assertNotNull($actual, $message ?: 'Expecting test to fail.');
        $this->assertStringContainsString($expected, $actual->getMessage());
    }
}
