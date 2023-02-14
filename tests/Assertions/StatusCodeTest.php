<?php
/*
 * Copyright 2023 Cloud Creativity Limited
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
