<?php

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
