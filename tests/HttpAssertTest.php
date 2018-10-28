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

    public function testOthers()
    {
        $this->markTestIncomplete('@todo must add tests for other assertions');
    }
}
