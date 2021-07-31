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

class HttpAssertTest extends TestCase
{

    public function testStatus(): void
    {
        HttpAssert::assertStatusCode('200', 200);

        $this->willFail(function () {
            HttpAssert::assertStatusCode('200', 400);
        });

        $this->willFail(function () {
            HttpAssert::assertStatusCode(400, '200', ['data' => null]);
        });

        $errors = <<<JSON_API
{
    "errors": [
        {
            "title": "Bad Request",
            "status": "400"
        }
    ]
}
JSON_API;

        $this->willFail(function () use ($errors) {
            HttpAssert::assertStatusCode(400, 200, $errors);
        });

        // do not expect an invalid JSON to break the assertion
        $this->willFail(function () {
           HttpAssert::assertStatusCode(400, 200, '{"data: null}');
        });
    }

    public function testHasError(): void
    {
        $content = <<<JSON_API
{
    "errors": [
        {
            "status": "422",
            "detail": "The selected bar is invalid.",
            "source": {
                "pointer": "/data/attributes/bar"
            }
        }
    ]
}
JSON_API;

        $message = new HttpMessage(422, 'application/vnd.api+json', $content);

        $subset = [
            'status' => '422',
            'source' => ['pointer' => '/data/attributes/bar'],
        ];

        $exact = $subset;
        $exact['detail'] = 'The selected bar is invalid.';

        $message->assertHasError(422);
        $message->assertHasError(422, $subset);
        $message->assertHasExactError(422, $exact);

        $this->willFail(function () use ($message) {
            $message->assertHasError(400);
        });

        $this->willFail(function () use ($message) {
            $message->assertHasError(422, [
                'status' => '422',
                'source' => ['pointer' => '/data/attributes/baz']
            ]);
        });

        $this->willFail(function () use ($message, $subset) {
            $message->assertHasExactError(422, $subset);
        });
    }

    public function testHasErrorWithMultipleErrors(): void
    {
        $content = <<<JSON_API
{
    "errors": [
        {
            "status": "404",
            "detail": "The related resource does not exist.",
            "source": {
                "pointer": "/data/relationships/foo"
            }
        },
        {
            "status": "422",
            "detail": "The selected bar is invalid.",
            "source": {
                "pointer": "/data/attributes/bar"
            }
        }
    ]
}
JSON_API;

        HttpAssert::assertHasError(400, 'application/vnd.api+json', $content, 400, [
            'status' => '422',
            'source' => ['pointer' => '/data/attributes/bar']
        ]);

        $this->willFail(function () use ($content) {
            HttpAssert::assertHasError(400, 'application/vnd.api+json', $content, 400, [
                'status' => '422',
                'source' => ['pointer' => '/data/attributes/baz']
            ]);
        });
    }

    public function testCreatedWithServerId(): void
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "Hello World!",
            "content": "..."
        }
    }
}
JSON_API;

        $expected = [
            'type' => 'posts',
            'attributes' => [
                'title' => 'Hello World!',
                'content' => '...',
            ],
        ];

        $message = new HttpMessage(201, 'application/vnd.api+json', $content, [
            'Location' => 'http://localhost/api/v1/posts/123',
        ]);

        $message->willSeeResourceType('posts');

        $message->assertCreatedWithServerId(
            'http://localhost/api/v1/posts',
            $expected
        );

        $expected['id'] = '123';

        $message->assertCreatedWithServerId(
            'http://localhost/api/v1/posts',
            $expected
        );

        $expected['id'] = '456';

        $this->willFail(function () use ($message, $expected) {
            $message->assertCreatedWithServerId(
                'http://localhost/api/v1/posts',
                $expected
            );
        });
    }

    public function testOthers()
    {
        $this->markTestIncomplete('@todo must add tests for other assertions');
    }
}
