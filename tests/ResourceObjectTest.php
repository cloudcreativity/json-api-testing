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
 * Class ResourceTesterTest
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class ResourceObjectTest extends TestCase
{

    public function testNull()
    {
        $content = [
            'data' => null,
        ];

        Assert::assertHasResourceObject($content, 'posts', '123');

        $this->willFail(function () use ($content) {
            Assert::assertHasResourceObject($content, 'posts', '123');
        });
    }

    public function testNoType()
    {
        $content = <<<JSON_API
{
    "data": {
        "id": "123",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $expected = [
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'title' => 'My First Post',
            ],
        ];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertResourceObjectSubset($content, $expected);
        });
    }

    public function testEmptyType()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "",
        "id": "123"
    }
}
JSON_API;

        $expected = ['type' => 'posts', 'id' => '123'];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertResourceObjectSubset($content, $expected);
        });
    }

    public function testTypeIs()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        Assert::assertResourceObjectSubset($content, ['type' => 'posts']);

        $this->willFail(function () use ($content) {
            Assert::assertResourceObjectSubset($content, ['type' => 'comments']);
        });
    }

    public function testHasResourceObject()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        Assert::assertHasResourceObject($content, 'posts', '123');

        $this->willFail(function () use ($content) {
            Assert::assertHasResourceObject($content, 'posts', '999');
        });

        $this->willFail(function () use ($content) {
            Assert::assertHasResourceObject($content, 'comments', '123');
        });
    }

    public function testNoId()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "attributes": {
            "title": "My First Post"
        }
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    public function testEmptyId()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": ""
    }
}
JSON_API;

        $document = DocumentTester::create($content);

        $this->willFail(function () use ($document) {
            $document->assertResource();
        });
    }

    public function testIdIs()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        Assert::assertResourceObjectSubset($content, ['id' => '123']);

        $this->willFail(function () use ($content) {
            Assert::assertResourceObjectSubset($content, ['id' => '999']);
        });
    }

    public function testAttributesSubset()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "My First Post",
            "tags": ["news", "misc"],
            "content": "This is my first post",
            "rank": 1
        }
    }
}
JSON_API;

        $expected = [
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'title' => 'My First Post',
                'tags' => ['news', 'misc'],
                'content' => 'This is my first post',
                'rank' => 1,
            ],
        ];

        Assert::assertResourceObjectSubset($content, $expected);

        $expected['attributes']['tags'] = ['news', 'other'];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertResourceObjectSubset($content, $expected);
        });
    }

    public function testRelationshipsSubset()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "relationships": {
            "author": {
                "data": {
                    "type": "users",
                    "id": "123"
                }
            },
            "comments": {
                "data": [
                    {"type": "comments", "id": "1"},
                    {"type": "comments", "id": "2"}
                ]
            }
        }
    }
}
JSON_API;

        $expected = [
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'users',
                        'id' => '123',
                    ],
                ],
                'comments' => [
                    'data' => [
                        ['type' => 'comments', 'id' => '1'],
                        ['type' => 'comments', 'id' => '2'],
                    ],
                ],
            ],
        ];

        Assert::assertResourceObjectSubset($content, $expected);

        $expected['relationships']['author'] = ['data' => ['id' => '456']];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertResourceObjectSubset($content, $expected);
        });
    }
}
