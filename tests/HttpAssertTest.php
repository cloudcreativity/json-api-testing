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

    public function testOthers()
    {
        $this->markTestIncomplete('@todo must add tests for other assertions');
    }
}
