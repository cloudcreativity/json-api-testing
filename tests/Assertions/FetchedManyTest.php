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
use Closure;
use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestModel;
use Illuminate\Support\Collection;

class FetchedManyTest extends TestCase
{
    /**
     * @var array
     */
    private array $post1;

    /**
     * @var array
     */
    private array $post2;

    /**
     * @var array
     */
    private array $post3;

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

        $this->post1 = [
            'type' => 'posts',
            'id' => '1',
            'attributes' => [
                'title' => 'My First Post',
                'content' => '...',
                'publishedAt' => Carbon::now(),
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'users',
                        'id' => '11',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/v1/posts/1/relationships/author',
                        'related' => 'http://localhost/api/v1/posts/1/author',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://localhost/api/v1/posts/1',
            ],
        ];

        $this->post2 = [
            'type' => 'posts',
            'id' => '2',
            'attributes' => [
                'title' => 'My Second Post',
                'content' => '###',
                'publishedAt' => Carbon::yesterday(),
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'users',
                        'id' => '22',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/v1/posts/2/relationships/author',
                        'related' => 'http://localhost/api/v1/posts/2/author',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://localhost/api/v1/posts/2',
            ],
        ];

        $this->post3 = [
            'type' => 'posts',
            'id' => '3',
            'attributes' => [
                'title' => 'My Second Post',
                'content' => '###',
                'publishedAt' => Carbon::now()->subWeek(),
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'users',
                        'id' => '33',
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/v1/posts/3/relationships/author',
                        'related' => 'http://localhost/api/v1/posts/3/author',
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://localhost/api/v1/posts/3',
            ],
        ];

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['data' => [$this->post1, $this->post2, $this->post3]]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testFetchedManyWithUrlRoutables(): void
    {
        $this->http->willSeeType('posts');

        $model1 = new TestModel((int) $this->post1['id']);
        $model2 = new TestModel((int) $this->post2['id']);
        $model3 = new TestModel((int) $this->post3['id']);
        $invalid = new TestModel((int) ($this->post3['id'] + 1));

        $models = [$model2, $model1, $model3]; // order is not asserted.

        $this->http->assertFetchedMany($models);
        $this->http->assertFetchedMany(Collection::make($models));

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$model1, $model3]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$model1, $invalid, $model3]),
        );
    }

    public function testFetchedManyWithIntegers(): void
    {
        $this->http->willSeeType('posts');

        $id1 = (int) $this->post1['id'];
        $id2 = (int) $this->post2['id'];
        $id3 = (int) $this->post3['id'];
        $invalid = $id3 + 1;

        $ids = [$id2, $id1, $id3]; // order is not asserted.

        $this->http->assertFetchedMany($ids);
        $this->http->assertFetchedMany(Collection::make($ids));

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$id1, $id3]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$id1, $invalid, $id3]),
        );
    }

    public function testFetchedManyWithStrings(): void
    {
        $this->http->willSeeType('posts');

        $id1 = $this->post1['id'];
        $id2 = $this->post2['id'];
        $id3 = $this->post3['id'];
        $invalid = strval($id3 + 1);

        $ids = [$id2, $id1, $id3]; // order is not asserted.

        $this->http->assertFetchedMany($ids);
        $this->http->assertFetchedMany(Collection::make($ids));

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$id1, $id3]),
        );

        $this->assertThatItFails(
            'array at [/data] only contains the subsets',
            fn() => $this->http->assertFetchedMany([$id1, $invalid, $id3]),
        );
    }

    /**
     * @return array
     */
    public function fetchedManyArrayProvider(): array
    {
        return [
            'identifiers (any order)' => [
                true,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                ],
            ],
            'identifiers invalid type' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => 'foobar',
                        'id' => $post3['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                ],
            ],
            'identifiers invalid id' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => strval($post3['id'] + 1),
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                ],
            ],
            'full resources (any order)' => [
                true,
                fn($post1, $post2, $post3) => [$post3, $post1, $post2],
            ],
            'partial resources (any order)' => [
                true,
                function ($post1, $post2, $post3) {
                    unset($post1['attributes']);
                    unset($post2['relationships']);
                    unset($post3['links']);
                    return [$post1, $post3, $post2];
                },
            ],
            'resources with invalid type' => [
                false,
                function ($post1, $post2, $post3) {
                    $post2['type'] = 'foobar';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid id' => [
                false,
                function ($post1, $post2, $post3) {
                    $post3['id'] = strval($post3['id'] + 1);
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['title'] = 'Blah!';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['published'] = true;
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['author']['data'] = [
                        'type' => 'users',
                        'id' => '999',
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['tags'] = [
                        'data' => [
                            [
                                'type' => 'tags',
                                'id' => '999'
                            ],
                        ],
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['self'] = $post3['links']['self'];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['foo'] = 'http://localhost/bar';
                    return [$post1, $post2, $post3];
                },
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedManyArrayProvider
     */
    public function testFetchedManyWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedMany($value);
        } else {
            $this->assertThatItFails(
                'array at [/data] only contains the subsets',
                fn() => $this->http->assertFetchedMany($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedManyArrayProvider
     */
    public function testFetchedManyWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedMany(new Collection($value));
        } else {
            $this->assertThatItFails(
                'array at [/data] only contains the subsets',
                fn() => $this->http->assertFetchedMany(new Collection($value))
            );
        }
    }

    public function testFetchedManyInOrderWithUrlRoutables(): void
    {
        $this->http->willSeeType('posts');

        $model1 = new TestModel((int) $this->post1['id']);
        $model2 = new TestModel((int) $this->post2['id']);
        $model3 = new TestModel((int) $this->post3['id']);
        $invalid = new TestModel((int) ($this->post3['id'] + 1));

        $models = [$model1, $model2, $model3];

        $this->http->assertFetchedManyInOrder($models);
        $this->http->assertFetchedManyInOrder(Collection::make($models));

        $this->assertThatItFails(
            'empty list at [/data]',
            fn() => $this->http->assertFetchedManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$model1, $model3]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$model1, $model3, $model2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$model1, $invalid, $model3]),
        );
    }

    public function testFetchedManyInOrderWithIntegers(): void
    {
        $this->http->willSeeType('posts');

        $id1 = (int) $this->post1['id'];
        $id2 = (int) $this->post2['id'];
        $id3 = (int) $this->post3['id'];
        $invalid = $id3 + 1;

        $ids = [$id1, $id2, $id3];

        $this->http->assertFetchedManyInOrder($ids);
        $this->http->assertFetchedManyInOrder(Collection::make($ids));

        $this->assertThatItFails(
            'empty list at [/data]',
            fn() => $this->http->assertFetchedManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $id3]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $id3, $id2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $invalid, $id3]),
        );
    }

    public function testFetchedManyInOrderWithStrings(): void
    {
        $this->http->willSeeType('posts');

        $id1 = $this->post1['id'];
        $id2 = $this->post2['id'];
        $id3 = $this->post3['id'];
        $invalid = strval($id3 + 1);

        $ids = [$id1, $id2, $id3];

        $this->http->assertFetchedManyInOrder($ids);
        $this->http->assertFetchedManyInOrder(Collection::make($ids));

        $this->assertThatItFails(
            'empty list at [/data]',
            fn() => $this->http->assertFetchedManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $id3]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $id3, $id2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the subsets in order',
            fn() => $this->http->assertFetchedManyInOrder([$id1, $invalid, $id3]),
        );
    }

    /**
     * @return array
     */
    public function fetchedManyInOrderArrayProvider(): array
    {
        return [
            'identifiers' => [
                true,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                ],
            ],
            'identifiers not in order' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                ],
            ],
            'identifiers invalid type' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => 'foobar',
                        'id' => $post2['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                ],
            ],
            'identifiers invalid id' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => strval($post2['id'] + 1),
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                ],
            ],
            'full resources' => [
                true,
                fn($post1, $post2, $post3) => [$post1, $post2, $post3],
            ],
            'full resources not in order' => [
                false,
                fn($post1, $post2, $post3) => [$post1, $post3, $post2],
            ],
            'partial resources' => [
                true,
                function ($post1, $post2, $post3) {
                    unset($post1['attributes']);
                    unset($post2['relationships']);
                    unset($post3['links']);
                    return [$post1, $post2, $post3];
                },
            ],
            'partial resources not in order' => [
                false,
                function ($post1, $post2, $post3) {
                    unset($post1['attributes']);
                    unset($post2['relationships']);
                    unset($post3['links']);
                    return [$post2, $post1, $post3];
                },
            ],
            'resources with invalid type' => [
                false,
                function ($post1, $post2, $post3) {
                    $post2['type'] = 'foobar';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid id' => [
                false,
                function ($post1, $post2, $post3) {
                    $post3['id'] = strval($post3['id'] + 1);
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['title'] = 'Blah!';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['published'] = true;
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['author']['data'] = [
                        'type' => 'users',
                        'id' => '999',
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['tags'] = [
                        'data' => [
                            [
                                'type' => 'tags',
                                'id' => '999'
                            ],
                        ],
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['self'] = $post3['links']['self'];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['foo'] = 'http://localhost/bar';
                    return [$post1, $post2, $post3];
                },
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedManyInOrderArrayProvider
     */
    public function testFetchedManyInOrderWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedManyInOrder($value);
        } else {
            $this->assertThatItFails(
                'array at [/data] contains the subsets in order',
                fn() => $this->http->assertFetchedManyInOrder($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedManyInOrderArrayProvider
     */
    public function testFetchedManyInOrderWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedManyInOrder(new Collection($value));
        } else {
            $this->assertThatItFails(
                'array at [/data] contains the subsets in order',
                fn() => $this->http->assertFetchedManyInOrder(new Collection($value))
            );
        }
    }

    public function testFetchedManyExactWithUrlRoutables(): void
    {
        $this->http->willSeeType('posts');

        $model1 = new TestModel((int) $this->post1['id']);
        $model2 = new TestModel((int) $this->post2['id']);
        $model3 = new TestModel((int) $this->post3['id']);

        $models = [$model1, $model2, $model3];

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedManyExact($models),
        );

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedManyExact(Collection::make($models)),
        );
    }

    public function testFetchedManyExactWithIntegers(): void
    {
        $this->http->willSeeType('posts');

        $id1 = (int) $this->post1['id'];
        $id2 = (int) $this->post2['id'];
        $id3 = (int) $this->post3['id'];

        $ids = [$id1, $id2, $id3];

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedManyExact($ids),
        );
    }

    public function testFetchedManyExactWithStrings(): void
    {
        $this->http->willSeeType('posts');

        $id1 = $this->post1['id'];
        $id2 = $this->post2['id'];
        $id3 = $this->post3['id'];

        $ids = [$id1, $id2, $id3];

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedManyExact($ids),
        );
    }

    /**
     * @return array
     */
    public function fetchedManyExactArrayProvider(): array
    {
        return [
            'identifiers' => [
                false,
                fn($post1, $post2, $post3) => [
                    [
                        'type' => $post1['type'],
                        'id' => $post1['id'],
                    ],
                    [
                        'type' => $post2['type'],
                        'id' => $post2['id'],
                    ],
                    [
                        'type' => $post3['type'],
                        'id' => $post3['id'],
                    ],
                ],
            ],
            'full resources in order' => [
                true,
                fn($post1, $post2, $post3) => [$post1, $post2, $post3],
            ],
            'full resources not in order' => [
                false,
                fn($post1, $post2, $post3) => [$post2, $post3, $post1],
            ],
            'partial resources in order' => [
                false,
                function ($post1, $post2, $post3) {
                    unset($post1['attributes']);
                    unset($post2['relationships']);
                    unset($post3['links']);
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid type' => [
                false,
                function ($post1, $post2, $post3) {
                    $post2['type'] = 'foobar';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid id' => [
                false,
                function ($post1, $post2, $post3) {
                    $post3['id'] = strval($post3['id'] + 1);
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['title'] = 'Blah!';
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional attribute' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['attributes']['published'] = true;
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['author']['data'] = [
                        'type' => 'users',
                        'id' => '999',
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional relationship' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['relationships']['tags'] = [
                        'data' => [
                            [
                                'type' => 'tags',
                                'id' => '999'
                            ],
                        ],
                    ];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with invalid link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['self'] = $post3['links']['self'];
                    return [$post1, $post2, $post3];
                },
            ],
            'resources with additional link' => [
                false,
                function ($post1, $post2, $post3) {
                    $post1['links']['foo'] = 'http://localhost/bar';
                    return [$post1, $post2, $post3];
                },
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedManyExactArrayProvider
     */
    public function testFetchedManyExactWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedManyExact($value);
        } else {
            $this->assertThatItFails(
                'member at [/data] exactly matches',
                fn() => $this->http->assertFetchedManyExact($value)
            );
        }
    }

    public function testFetchedNone(): void
    {
        $empty = $this->http->withContent('{"data": []}');

        $empty->assertFetchedNone();

        $this->assertThatItFails(
            'document has an empty list at [/data]',
            fn() => $this->http->assertFetchedNone()
        );
    }

    public function testInvalidStatusCode(): void
    {
        $http = $this->http->withStatusCode(201);
        $empty = $http->withContent('{"data": []}');
        $expected = [$this->post1, $this->post2, $this->post3];

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedMany($expected)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedManyExact($expected)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedManyInOrder($expected)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $empty->assertFetchedNone()
        );
    }

    public function testInvalidContentType(): void
    {
        $http = $this->http->withContentType('application/json');
        $empty = $http->withContent('{"data": []}');
        $expected = [$this->post1, $this->post2, $this->post3];

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedMany($expected)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedManyExact($expected)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedManyInOrder($expected)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $empty->assertFetchedNone()
        );
    }
}
