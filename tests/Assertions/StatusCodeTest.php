<?php
/*
 * Copyright 2025 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;

class StatusCodeTest extends TestCase
{

    public function testWithoutContent(): void
    {
        $http = new HttpMessage(201, null, null, []);

        $http->assertStatusCode(201);

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertStatusCode(200)
        );
    }

    public function testWithContent(): void
    {
        $json = <<<JSON
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "Hello World!"
        }
    }
}
JSON;


        $http = new HttpMessage(200, 'application/vnd.api+json', $json, [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $http->assertStatusCode(200);

        $this->assertThatItFails(
            'status 200 is 400',
            fn() => $http->assertStatusCode(400)
        );
    }

    /**
     * Check the assertion does not fail with invalid JSON content.
     *
     * Because the JSON is output in the assertion message, we need to check that
     * invalid JSON in the request does not break things.
     *
     * @return void
     */
    public function testWithInvalidJsonContent(): void
    {
        $http = new HttpMessage(200, 'application/vnd.api+json', '{"data: null}', [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $http->assertStatusCode(200);
        $this->assertThatItFails(
            'status 200 is 400',
            fn() => $http->assertStatusCode(400)
        );
    }
}
