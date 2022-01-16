<?php

/*
 * Copyright 2022 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing\Tests;

use CloudCreativity\JsonApi\Testing\Assert;
use CloudCreativity\JsonApi\Testing\Document;

/**
 * Class AssertDataTest
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

        Assert::assertNull($content);

        $this->willFail(function () use ($content) {
            Assert::assertResource($content, 'posts', '123');
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
            Assert::assertHash($content, $expected);
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
            Assert::assertHash($content, $expected);
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

        Assert::assertHash($content, ['type' => 'posts']);

        $this->willFail(function () use ($content) {
            Assert::assertHash($content, ['type' => 'comments']);
        });
    }

    public function testDataIs()
    {
        $content = <<<JSON_API
{
    "data": {
        "type": "posts",
        "id": "123"
    }
}
JSON_API;

        $document = Document::decode($content);

        $document->assertResource('posts', '123');

        $this->willFail(function () use ($document) {
            $document->assertResource('posts', '999');
        });

        $this->willFail(function () use ($document) {
            $document->assertResource('comments', '123');
        });

        $this->willFail(function () use ($document) {
            $document->assertNull();
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
            Assert::assertResource($content, 'posts', '123');
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

        $document = Document::decode($content);

        $this->willFail(function () use ($document) {
            $document->assertResource('posts', '123');
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

        Assert::assertHash($content, ['id' => '123']);

        $this->willFail(function () use ($content) {
            Assert::assertHash($content, ['id' => '999']);
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

        Assert::assertHash($content, $expected);

        $expected['attributes']['tags'] = ['news', 'other'];

        $this->willFail(function () use ($content, $expected) {
            Assert::assertHash($content, $expected);
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

        $document = Document::decode($content);

        $document->assertHash($expected);

        $expected['relationships']['author'] = ['data' => ['id' => '456']];

        $this->willFail(function () use ($document, $expected) {
            $document->assertHash($expected);
        });
    }

    public function testExactData()
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

        $document = Document::decode($content);

        $document->assertExact($expected);

        unset($expected['attributes']['content']);

        $document->assertHash($expected); // subset will pass.

        $this->willFail(function () use ($document, $expected) {
            $document->assertExact($expected);
        });
    }

    public function testArray()
    {
        $content = <<<JSON_API
{
    "data": [
        {
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
        },
        {
            "type": "posts",
            "id": "456",
            "attributes": {
                "title": "My Second Post",
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
                        {"type": "comments", "id": "101"}
                    ]
                }
            },
            "links": {
                "self": "/api/v1/posts/123"
            }
        }
    ]
}
JSON_API;

        $document = Document::decode($content);

        $expected = json_decode($content, true)['data'];

        $ids = [
            ['type' => 'posts', 'id' => '123'],
            ['type' => 'posts', 'id' => '456'],
        ];

        $notOrdered = [
            ['type' => 'posts', 'id' => '456'],
            ['type' => 'posts', 'id' => '123'],
        ];

        $document->assertExact($expected);
        $document->assertListInOrder($ids);
        $document->assertList($notOrdered);
        $document->assertListNotEmpty();
        $document->assertListNotEmptyOrMissing();

        $document->assertListContainsResource('posts', '456');
        $document->assertListContainsResource(
            'comments',
            '101',
            '/data/1/relationships/comments/data'
        );

        $this->willFail(function () use ($document, $expected) {
            unset($expected[1]['relationships']['comments']);
            $document->assertExact($expected);
        });

        /** Assert data should fail if not in the correct order. */
        $this->willFail(function () use ($document, $notOrdered) {
            $document->assertListInOrder($notOrdered);
        });

        /** Assert list should fail if there is an id not in the array */
        $this->willFail(function () use ($document, $notOrdered) {
            $notOrdered[] = ['type' => 'posts', 'id' => '999'];
            $document->assertList($notOrdered);
        });

        $this->willFail(function () use ($document) {
            $document->assertListContainsResource('comments', '101');
        });

        $this->willFail(function () use ($document) {
            $document->assertListEmpty();
        });

        $this->willFail(function () use ($document) {
            $document->assertListEmptyOrMissing();
        });
    }
}
