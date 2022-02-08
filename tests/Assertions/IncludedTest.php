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

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use Carbon\Carbon;
use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestModel;
use Illuminate\Support\Collection;

class IncludedTest extends TestCase
{

    /**
     * @var array
     */
    private array $post = [
        'type' => 'posts',
        'id' => '1',
        'attributes' => [
            'title' => 'Hello World!',
            'content' => '...',
        ],
        'relationships' => [
            'author' => [
                'data' => [
                    'type' => 'users',
                    'id' => '2',
                ],
            ],
            'tags' => [
                'data' => [
                    [
                        'type' => 'tags',
                        'id' => '3',
                    ],
                    [
                        'type' => 'tags',
                        'id' => '4',
                    ],
                ],
            ],
        ],
        'links' => [
            'self' => '/api/v1/posts/1',
        ],
    ];

    /**
     * @var array
     */
    private array $author;

    /**
     * @var array
     */
    private array $tag1 = [
        'type' => 'tags',
        'id' => '3',
        'attributes' => [
            'displayName' => 'Laravel',
        ],
    ];

    /**
     * @var array
     */
    private array $tag2 = [
        'type' => 'tags',
        'id' => '4',
        'attributes' => [
            'displayName' => 'JSON:API',
        ],
    ];

    /**
     * @var HttpMessage
     */
    private HttpMessage $http;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->author = [
            'type' => 'users',
            'id' => '2',
            'attributes' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'registeredAt' => Carbon::yesterday(),
            ],
        ];

        $document = [
            'data' => $this->post,
            'included' => [
                $this->author,
                $this->tag1,
                $this->tag2,
            ],
        ];

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode($document),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );

        $this->http->willSeeType($this->post['type']);
    }

    /**
     * @return array
     */
    public function isIncludedProvider(): array
    {
        return [
            'author' => [
                true,
                'users',
                '2',
            ],
            'tag 1' => [
                true,
                'tags',
                '3',
            ],
            'tag 2' => [
                true,
                'tags',
                '4',
            ],
            'wrong author' => [
                false,
                'users',
                '5',
            ],
            'wrong tag' => [
                false,
                'tags',
                '6',
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param string $type
     * @param string $id
     * @return void
     * @dataProvider isIncludedProvider
     */
    public function testIsIncludedWithUrlRoutable(bool $expected, string $type, string $id): void
    {
        $model = new TestModel((int) $id);

        if ($expected) {
            $this->http->assertIsIncluded($type, $model);
        } else {
            $this->assertThatItFails(
                'the array at [/included] contains the subset',
                fn() => $this->http->assertIsIncluded($type, $model)
            );
        }
    }

    /**
     * @param bool $expected
     * @param string $type
     * @param string $id
     * @return void
     * @dataProvider isIncludedProvider
     */
    public function testIsIncludedWithString(bool $expected, string $type, string $id): void
    {
        if ($expected) {
            $this->http->assertIsIncluded($type, $id);
        } else {
            $this->assertThatItFails(
                'the array at [/included] contains the subset',
                fn() => $this->http->assertIsIncluded($type, $id)
            );
        }
    }

    /**
     * @param bool $expected
     * @param string $type
     * @param string $id
     * @return void
     * @dataProvider isIncludedProvider
     */
    public function testIsIncludedWithInteger(bool $expected, string $type, string $id): void
    {
        $id = (int) $id;

        if ($expected) {
            $this->http->assertIsIncluded($type, $id);
        } else {
            $this->assertThatItFails(
                'the array at [/included] contains the subset',
                fn() => $this->http->assertIsIncluded($type, $id)
            );
        }
    }

    public function testIncludedWithUrlRoutables(): void
    {
        $author = new TestModel((int) $this->author['id']);
        $tag1 = new TestModel((int) $this->tag1['id']);
        $tag2 = new TestModel((int) $this->tag2['id']);
        $invalid = new TestModel(99);

        // order is not significant.
        $this->http->assertIncluded($values = [
            ['type' => 'tags', 'id' => $tag2],
            ['type' => 'users', 'id' => $author],
            ['type' => 'tags', 'id' => $tag1],
        ]);

        $this->http->assertIncluded(Collection::make($values));

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'users', 'id' => $author],
            ])
        );

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'users', 'id' => $author],
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'tags', 'id' => $invalid],
            ])
        );
    }

    public function testIncludedWithIntegers(): void
    {
        $author = (int) $this->author['id'];
        $tag1 = (int) $this->tag1['id'];
        $tag2 = (int) $this->tag2['id'];
        $invalid = 99;

        // order is not significant.
        $this->http->assertIncluded($values = [
            ['type' => 'tags', 'id' => $tag2],
            ['type' => 'users', 'id' => $author],
            ['type' => 'tags', 'id' => $tag1],
        ]);

        $this->http->assertIncluded(Collection::make($values));

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'users', 'id' => $author],
            ])
        );

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'users', 'id' => $author],
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'tags', 'id' => $invalid],
            ])
        );
    }

    public function testIncludedWithStrings(): void
    {
        $author = $this->author['id'];
        $tag1 = $this->tag1['id'];
        $tag2 = $this->tag2['id'];
        $invalid = '99';

        // order is not significant.
        $this->http->assertIncluded($values = [
            ['type' => 'tags', 'id' => $tag2],
            ['type' => 'users', 'id' => $author],
            ['type' => 'tags', 'id' => $tag1],
        ]);

        $this->http->assertIncluded(Collection::make($values));

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'users', 'id' => $author],
            ])
        );

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                ['type' => 'users', 'id' => $author],
                ['type' => 'tags', 'id' => $tag1],
                ['type' => 'tags', 'id' => $invalid],
            ])
        );
    }

    public function testIncludedWithResources(): void
    {
        $this->http->assertIncluded([$this->author, $this->tag1, $this->tag2]);

        // order is not significant
        $this->http->assertIncluded(new Collection([
            $this->tag1,
            $this->author,
            $this->tag2,
        ]));

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                $this->tag2,
                $this->author,
            ])
        );

        $this->assertThatItFails(
            'array at [/included] only contains the subsets',
            fn() => $this->http->assertIncluded([
                $this->author,
                $this->tag2,
                $this->post,
            ])
        );
    }

    public function testDoesntHaveIncluded(): void
    {
        $none = $this->http->withContent(json_encode(['data' => $this->post]));
        $none->assertDoesntHaveIncluded();

        $this->assertThatItFails(
            'Document has included resources',
            fn() => $this->http->assertDoesntHaveIncluded()
        );
    }
}
