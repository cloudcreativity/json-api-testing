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
class AssertDataTest extends TestCase
{

    public function testNull()
    {
        $content = [
            'data' => null,
        ];

        Assert::assertDataIsNull($content);

        $this->willFail(function () use ($content) {
            Assert::assertDocumentHasResourceObject($content, 'posts', '123');
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
            Assert::assertData($content, $expected);
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
            Assert::assertData($content, $expected);
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

        Assert::assertData($content, ['type' => 'posts']);

        $this->willFail(function () use ($content) {
            Assert::assertData($content, ['type' => 'comments']);
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

        Assert::assertDocumentHasResourceObject($content, 'posts', '123');

        $this->willFail(function () use ($content) {
            Assert::assertDocumentHasResourceObject($content, 'posts', '999');
        });

        $this->willFail(function () use ($content) {
            Assert::assertDocumentHasResourceObject($content, 'comments', '123');
        });

        $this->willFail(function () use ($content) {
            Assert::assertDataIsNull($content);
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

        $this->willFail(function () use ($content) {
            Assert::assertDocumentHasResourceObject($content, 'posts', '123');
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

        $this->willFail(function () use ($content) {
            Assert::assertDocumentHasResourceObject($content, 'posts', '123');
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

        Assert::assertData($content, ['id' => '123']);

        $this->willFail(function () use ($content) {
            Assert::assertData($content, ['id' => '999']);
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

        Assert::assertData($content, $expected);

        $expected['attributes']['tags'] = ['news', 'other'];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertData($content, $expected);
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

        Assert::assertData($content, $expected);

        $expected['relationships']['author'] = ['data' => ['id' => '456']];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertData($content, $expected);
        });
    }

    public function testExactResourceObject()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123",
        "attributes": {
            "title": "My First Post",
            "content": "..."
        },
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
        },
        "links": {
            "self": "/api/v1/posts/123"
        }
    }
}
JSON_API;

        $expected = [
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'title' => 'My First Post',
                'content' => '...',
            ],
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
            'links' => [
                'self' => '/api/v1/posts/123',
            ],
        ];

        Assert::assertExactData($content, $expected);

        unset($expected['attributes']['content']);

        Assert::assertData($content, $expected); // as this is a subset, it will pass.

        $this->willFail(function () use ($content, $expected) {
            Assert::assertExactData($content, $expected);
        });
    }
}
