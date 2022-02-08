<?php
/*
 * Copyright 2022 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing\Tests\Utils;

use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use CloudCreativity\JsonApi\Testing\Utils\JsonStack;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Collection;

class JsonStackTest extends TestCase
{

    public function testUrlRoutable(): void
    {
        $model1 = $this->createMock(UrlRoutable::class);
        $model1->method('getRouteKey')->willReturn(1);
        $model2 = $this->createMock(UrlRoutable::class);
        $model2->method('getRouteKey')->willReturn(2);

        $expected = <<<JSON
[
    {
        "type": "posts",
        "id": "1"
    },
    {
        "type": "posts",
        "id": "2"
    }
]
JSON;

        $stack = new JsonStack([$model1, $model2], 'posts');
        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }

    public function testIntegers(): void
    {
        $expected = <<<JSON
[
    {
        "type": "posts",
        "id": "1"
    },
    {
        "type": "posts",
        "id": "2"
    }
]
JSON;

        $stack = new JsonStack([1, 2], 'posts');
        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }

    public function testStrings(): void
    {
        $expected = <<<JSON
[
    {
        "type": "posts",
        "id": "1"
    },
    {
        "type": "posts",
        "id": "2"
    }
]
JSON;

        $stack = new JsonStack(new Collection(['1', '2']), 'posts');
        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }

    public function testArrayWithUrlRoutables(): void
    {
        $model1 = $this->createMock(UrlRoutable::class);
        $model1->method('getRouteKey')->willReturn(1);
        $model2 = $this->createMock(UrlRoutable::class);
        $model2->method('getRouteKey')->willReturn(2);

        $expected = <<<JSON
[
    {
        "type": "posts",
        "id": "1"
    },
    {
        "type": "comments",
        "id": "2"
    }
]
JSON;

        $stack = new JsonStack([
            ['type' => 'posts', 'id' => $model1],
            ['type' => 'comments', 'id' => $model2],
        ]);
        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }

    public function testArrayWithIntegerAndStringIds(): void
    {
        $expected = <<<JSON
[
    {
        "type": "posts",
        "id": "1"
    },
    {
        "type": "comments",
        "id": "2"
    }
]
JSON;

        $stack = new JsonStack([
            ['type' => 'posts', 'id' => 1],
            ['type' => 'comments', 'id' => '2'],
        ]);
        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }

    public function testArraysAndObjects(): void
    {
        $resource1 = [
            'type' => 'posts',
            'id' => '1',
            'attributes' => [
                'title' => 'Hello World',
                'content' => '...',
            ],
        ];

        $resource2 = [
            'type' => 'comments',
            'id' => '2',
            'attributes' => [
                'content' => 'Great blog post!',
            ],
        ];

        $expected = json_encode([$resource1, $resource2]);

        $stack = new JsonStack([
            $resource1,
            new TestObject($resource2),
        ]);

        $this->assertJsonStringEqualsJsonString($expected, $stack->toJson());
    }
}
