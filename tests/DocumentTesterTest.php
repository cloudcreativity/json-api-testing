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

/**
 * Class DocumentTesterTest
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class DocumentTesterTest extends TestCase
{

    public function testInvalidJson()
    {
        $this->willFail(function () {
            DocumentTester::create('{ "data": []');
        });
    }

    public function testInvalidJsonObject()
    {
        $this->willFail(function () {
            DocumentTester::create('null');
        });
    }

    public function testDataIsNull()
    {
        DocumentTester::create('{"data": null}')->assertDataNull();
    }

    public function testDataIsNullWithData()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertDataNull();
        });
    }

    public function testDataIsNullWithoutData()
    {
        $document = DocumentTester::create('{ "errors": [] }');

        $this->willFail(function () use ($document) {
            $document->assertDataNull();
        });
    }

    public function testIncludedResources()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "1",
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "2"
                }
            }
        }
    },
    "included": [
        {
            "type": "users",
            "id": "2",
            "attributes": {}
        }
    ]
}
JSON_API;

        $document = DocumentTester::create($content);

        $included = $document->assertIncluded();

        $this->assertInstanceOf(ResourceObjectsTester::class, $included);
        $included->assertResource('users', 2);
    }

    public function testIncludedIsNotPresent()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertIncluded();
        });
    }

    public function testMeta()
    {
        $content = <<<JSON_API
{
    "meta": {
        "page": 1,
        "size": 15,
        "last": 10
    },
    "data": []
}
JSON_API;

        $document = DocumentTester::create($content);

        $document->assertMetaSubset(['page' => 1, 'size' => 15]);
        $document->assertMetaIs(['page' => 1, 'size' => 15, 'last' => 10]);

        $this->willFail(function () use ($document) {
            $document->assertMetaSubset(['page' => 2]);
        });

        $this->willFail(function () use ($document) {
            $document->assertMetaIs(['page' => 2, 'size' => 15, 'last' => 10]);
        });
    }
}
