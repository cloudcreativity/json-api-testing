<?php
/*
 * Copyright 2024 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use Closure;
use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestModel;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;

class FetchedToManyTest extends TestCase
{

    /**
     * @var array
     */
    private array $post1 = [
        'type' => 'posts',
        'id' => '1',
    ];

    /**
     * @var array
     */
    private array $post2 = [
        'type' => 'posts',
        'id' => '2',
    ];

    /**
     * @var array
     */
    private array $post3 = [
        'type' => 'posts',
        'id' => '3',
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

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['data' => [$this->post1, $this->post2, $this->post3]]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testFetchedToManyWithUrlRoutables(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $model1 = new TestModel((int) $this->post1['id']);
        $model2 = new TestModel((int) $this->post2['id']);
        $model3 = new TestModel((int) $this->post3['id']);
        $invalid = new TestModel((int) ($this->post3['id'] + 1));

        $models = [$model2, $model1, $model3]; // order is not asserted.

        $this->http->assertFetchedToMany($models);
        $this->http->assertFetchedToMany(Collection::make($models));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToMany([]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$model1, $model3]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$model1, $invalid, $model3]),
        );
    }

    public function testFetchedToManyWithIntegers(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $id1 = (int) $this->post1['id'];
        $id2 = (int) $this->post2['id'];
        $id3 = (int) $this->post3['id'];
        $invalid = $id3 + 1;

        $ids = [$id2, $id1, $id3]; // order is not asserted.

        $this->http->assertFetchedToMany($ids);
        $this->http->assertFetchedToMany(Collection::make($ids));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToMany([]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$id1, $id3]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$id1, $invalid, $id3]),
        );
    }

    public function testFetchedToManyWithStrings(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $id1 = $this->post1['id'];
        $id2 = $this->post2['id'];
        $id3 = $this->post3['id'];
        $invalid = strval($id3 + 1);

        $ids = [$id2, $id1, $id3]; // order is not asserted.

        $this->http->assertFetchedToMany($ids);
        $this->http->assertFetchedToMany(Collection::make($ids));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToMany([]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$id1, $id3]),
        );

        $this->assertThatItFails(
            'the list at [/data] only contains the values',
            fn() => $this->http->assertFetchedToMany([$id1, $invalid, $id3]),
        );
    }

    /**
     * @return array[]
     */
    public static function fetchedToManyArrayProvider(): array
    {
        return [
            'in order' => [
                true,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    $post2,
                    $post3,
                ],
            ],
            'not in order' => [
                true,
                fn(array $post1, array $post2, array $post3): array => [
                    $post3,
                    $post1,
                    $post2,
                ],
            ],
            'invalid type' => [
                false,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    ['type' => 'foobar', 'id' => $post2['id']],
                    $post3,
                ],
            ],
            'invalid id' => [
                false,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    ['type' => $post2['type'], 'id' => strval($post2['id'] * 10)],
                    $post3,
                ],
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedToManyArrayProvider')]
    public function testFetchedToManyWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedToMany($value);
        } else {
            $this->assertThatItFails(
                'the list at [/data] only contains the values',
                fn() => $this->http->assertFetchedToMany($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedToManyArrayProvider')]
    public function testFetchedToManyWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedToMany($value);
            $this->http->assertFetchedToMany(new Collection($value));
        } else {
            $this->assertThatItFails(
                'the list at [/data] only contains the values',
                fn() => $this->http->assertFetchedToMany(new Collection($value))
            );
        }
    }

    public function testFetchedToManyWithResources(): void
    {
        [$post1, $post2, $post3] = $this->createResources();

        $http = $this->http->withContent(json_encode(['data' => [$post1, $post2, $post3]]));

        $this->assertThatItFails(
            'list at [/data] only contains the values',
            fn() => $http->assertFetchedToMany([$this->post1, $this->post2, $this->post3]),
        );
    }

    public function testFetchedToManyInOrderWithUrlRoutables(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $model1 = new TestModel((int) $this->post1['id']);
        $model2 = new TestModel((int) $this->post2['id']);
        $model3 = new TestModel((int) $this->post3['id']);
        $invalid = new TestModel((int) ($this->post3['id'] + 1));

        $models = [$model1, $model2, $model3];

        $this->http->assertFetchedToManyInOrder($models);
        $this->http->assertFetchedToManyInOrder(Collection::make($models));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$model1, $model3, $model2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$model1, $invalid, $model3]),
        );
    }

    public function testFetchedToManyInOrderWithIntegers(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $id1 = (int) $this->post1['id'];
        $id2 = (int) $this->post2['id'];
        $id3 = (int) $this->post3['id'];
        $invalid = $id3 + 1;

        $ids = [$id1, $id2, $id3];

        $this->http->assertFetchedToManyInOrder($ids);
        $this->http->assertFetchedToManyInOrder(Collection::make($ids));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$id1, $id3, $id2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$id1, $invalid, $id3]),
        );
    }

    public function testFetchedToManyInOrderWithStrings(): void
    {
        $this->http->willSeeType($this->post1['type']);

        $id1 = $this->post1['id'];
        $id2 = $this->post2['id'];
        $id3 = $this->post3['id'];
        $invalid = strval($id3 + 1);

        $ids = [$id1, $id2, $id3];

        $this->http->assertFetchedToManyInOrder($ids);
        $this->http->assertFetchedToManyInOrder(Collection::make($ids));

        $this->assertThatItFails(
            'the document has an empty list at [/data]',
            fn() => $this->http->assertFetchedToManyInOrder([]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$id1, $id3, $id2]),
        );

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $this->http->assertFetchedToManyInOrder([$id1, $invalid, $id3]),
        );
    }

    /**
     * @return array[]
     */
    public static function fetchedToManyInOrderArrayProvider(): array
    {
        return [
            'in order' => [
                true,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    $post2,
                    $post3,
                ],
            ],
            'not in order' => [
                false,
                fn(array $post1, array $post2, array $post3): array => [
                    $post3,
                    $post1,
                    $post2,
                ],
            ],
            'invalid type' => [
                false,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    ['type' => 'foobar', 'id' => $post2['id']],
                    $post3,
                ],
            ],
            'invalid id' => [
                false,
                fn(array $post1, array $post2, array $post3): array => [
                    $post1,
                    ['type' => $post2['type'], 'id' => strval($post2['id'] * 10)],
                    $post3,
                ],
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedToManyInOrderArrayProvider')]
    public function testFetchedToManyInOrderWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedToManyInOrder($value);
        } else {
            $this->assertThatItFails(
                'array at [/data] contains the resource identifiers in order',
                fn() => $this->http->assertFetchedToManyInOrder($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedToManyInOrderArrayProvider')]
    public function testFetchedToManyInOrderWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->post1, $this->post2, $this->post3);

        if ($expected) {
            $this->http->assertFetchedToManyInOrder(new Collection($value));
        } else {
            $this->assertThatItFails(
                'array at [/data] contains the resource identifiers in order',
                fn() => $this->http->assertFetchedToManyInOrder(new Collection($value))
            );
        }
    }

    public function testFetchedToManyInOrderWithResources(): void
    {
        [$post1, $post2, $post3] = $this->createResources();

        $http = $this->http->withContent(json_encode(['data' => [$post1, $post2, $post3]]));

        $this->assertThatItFails(
            'array at [/data] contains the resource identifiers in order',
            fn() => $http->assertFetchedToManyInOrder([$this->post1, $this->post2, $this->post3]),
        );
    }

    public function testInvalidStatusCode(): void
    {
        $http = $this->http->withStatusCode(201);
        $expected = [$this->post1, $this->post2, $this->post3];

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedToMany($expected)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedToManyInOrder($expected)
        );
    }

    public function testInvalidContentType(): void
    {
        $http = $this->http->withContentType('application/json');
        $expected = [$this->post1, $this->post2, $this->post3];

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedToMany($expected)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedToManyInOrder($expected)
        );
    }

    /**
     * @return array[]
     */
    private function createResources(): array
    {
        $post1 = [
            'type' => $this->post1['type'],
            'id' => $this->post1['id'],
            'attributes' => [
                'foo' => 'bar',
            ],
            'relationships' => [
                'baz' => [
                    'data' => null,
                ],
            ],
            'links' => [
                'self' => sprintf('/api/v1/%s/%d', $this->post1['type'], $this->post1['id'])
            ],
        ];

        $post2 = [
            'type' => $this->post2['type'],
            'id' => $this->post2['id'],
            'attributes' => [
                'foo' => 'bar',
            ],
        ];

        $post3 = [
            'type' => $this->post1['type'],
            'id' => $this->post1['id'],
            'relationships' => [
                'baz' => [
                    'data' => null,
                ],
            ],
        ];

        return [$post1, $post2, $post3];
    }
}
