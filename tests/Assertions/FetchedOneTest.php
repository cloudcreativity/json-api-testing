<?php
/*
 * Copyright 2025 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use Carbon\Carbon;
use Closure;
use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestModel;
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;

class FetchedOneTest extends TestCase
{

    /**
     * @var array
     */
    private array $resource;

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

        $this->resource = [
            'type' => 'posts',
            'id' => '1',
            'attributes' => [
                'title' => 'Hello World!',
                'content' => '...',
                'rating' => 4.0,
                'publishedAt' => Carbon::yesterday(),
            ],
            'relationships' => [
                'tags' => [
                    'data' => [
                        [
                            'type' => 'tags',
                            'id' => '2',
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => '/api/v1/posts/1',
            ],
        ];

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['data' => $this->resource]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testFetchedOneWithUrlRoutable(): void
    {
        $this->http->willSeeType($this->resource['type']);

        $model = new TestModel((int) $this->resource['id']);

        $this->http->assertFetchedOne($model);
        $this->http->assertFetchedOne([
            'type' => $this->resource['type'],
            'id' => $model,
        ]);

        $invalid = new TestModel($this->resource['id'] + 1);

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedOne($invalid)
        );

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedOne([
                'type' => $this->resource['type'],
                'id' => $invalid,
            ])
        );
    }

    public function testFetchedOneWithIntegerAndString(): void
    {
        $this->http->willSeeType($this->resource['type']);

        $this->http->assertFetchedOne($this->resource['id']);
        $this->http->assertFetchedOne((int) $this->resource['id']);

        $invalid = intval($this->resource['id']) + 1;

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedOne((string) $invalid)
        );

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedOne($invalid)
        );
    }

    /**
     * @return array
     */
    public static function fetchedOneArrayProvider(): array
    {
        return [
            'identifier' => [
                true,
                fn(array $resource) => [
                    'type' => $resource['type'],
                    'id' => $resource['id'],
                ],
            ],
            'identifier invalid type' => [
                false,
                fn(array $resource) => [
                    'type' => 'foobar',
                    'id' => $resource['id'],
                ],
            ],
            'identifier invalid id' => [
                false,
                fn(array $resource) => [
                    'type' => $resource['type'],
                    'id' => strval($resource['id'] + 1)
                ],
            ],
            'full resource' => [
                true,
                fn(array $resource) => $resource,
            ],
            'resource w/o attributes' => [
                true,
                function (array $resource) {
                    unset($resource['attributes']);
                    return $resource;
                },
            ],
            'resource w/o relationships' => [
                true,
                function (array $resource) {
                    unset($resource['relationships']);
                    return $resource;
                },
            ],
            'resource w/o links' => [
                true,
                function (array $resource) {
                    unset($resource['links']);
                    return $resource;
                },
            ],
            'resource invalid type' => [
                false,
                function (array $resource) {
                    $resource['type'] = 'foobar';
                    return $resource;
                },
            ],
            'resource invalid id' => [
                false,
                function (array $resource) {
                    $resource['id'] = strval($resource['id'] + 1);
                    return $resource;
                },
            ],
            'resource invalid attribute' => [
                false,
                function (array $resource) {
                    $resource['attributes']['title'] = 'Unexpected!';
                    return $resource;
                },
            ],
            'resource with additional attribute' => [
                false,
                function (array $resource) {
                    $resource['attributes']['published'] = true;
                    return $resource;
                },
            ],
            'resource invalid relationship' => [
                false,
                function (array $resource) {
                    $resource['relationships']['tags']['data'] = [
                        [
                            'type' => 'foobar',
                            'id' => '2',
                        ],
                    ];
                    return $resource;
                },
            ],
            'resource with additional relationship' => [
                false,
                function (array $resource) {
                    $resource['relationships']['author'] = [
                        'data' => [
                            'type' => 'users',
                            'id' => '3',
                        ],
                    ];
                    return $resource;
                },
            ],
            'resource invalid links' => [
                false,
                function (array $resource) {
                    $resource['link'] = '/api/v1/foobars/123';
                    return $resource;
                },
            ],
            'resource with additional link' => [
                false,
                function (array $resource) {
                    $resource['links']['foo'] = '/api/v1/posts/1/foo';
                    return $resource;
                },
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedOneArrayProvider')]
    public function testFetchedOneWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->resource);

        if ($expected) {
            $this->http->assertFetchedOne($value);
        } else {
            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedOne($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedOneArrayProvider')]
    public function testFetchedOneWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->resource);

        if ($expected) {
            $this->http->assertFetchedOne(new TestObject($value));
            $this->http->assertFetchedOne(new Collection($value));
        } else {
            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedOne(new TestObject($value))
            );

            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedOne(new Collection($value))
            );
        }
    }

    public function testFetchOneExactWithUrlRoutable(): void
    {
        $this->http->willSeeType($this->resource['type']);

        $model = new TestModel((int) $this->resource['id']);

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedOneExact($model)
        );
    }

    public function testFetchOneExactWithStringAndInteger(): void
    {
        $this->http->willSeeType($this->resource['type']);

        $id = $this->resource['id'];

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedOneExact($id)
        );

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedOneExact((int) $id)
        );
    }

    /**
     * @return array
     */
    public static function fetchedOneExactArrayProvider(): array
    {
        return [
            'identifier' => [
                false,
                fn(array $resource) => [
                    'type' => $resource['type'],
                    'id' => $resource['id'],
                ],
            ],
            'full resource' => [
                true,
                fn(array $resource) => $resource,
            ],
            'resource w/o attributes' => [
                false,
                function (array $resource) {
                    unset($resource['attributes']);
                    return $resource;
                },
            ],
            'resource w/o relationships' => [
                false,
                function (array $resource) {
                    unset($resource['relationships']);
                    return $resource;
                },
            ],
            'resource w/o links' => [
                false,
                function (array $resource) {
                    unset($resource['links']);
                    return $resource;
                },
            ],
            'resource invalid type' => [
                false,
                function (array $resource) {
                    $resource['type'] = 'foobar';
                    return $resource;
                },
            ],
            'resource invalid id' => [
                false,
                function (array $resource) {
                    $resource['id'] = strval($resource['id'] + 1);
                    return $resource;
                },
            ],
            'resource invalid attribute' => [
                false,
                function (array $resource) {
                    $resource['attributes']['title'] = 'Unexpected!';
                    return $resource;
                },
            ],
            'resource with additional attribute' => [
                false,
                function (array $resource) {
                    $resource['attributes']['published'] = true;
                    return $resource;
                },
            ],
            'resource invalid relationship' => [
                false,
                function (array $resource) {
                    $resource['relationships']['tags']['data'] = [
                        [
                            'type' => 'foobar',
                            'id' => '2',
                        ],
                    ];
                    return $resource;
                },
            ],
            'resource with additional relationship' => [
                false,
                function (array $resource) {
                    $resource['relationships']['author'] = [
                        'data' => [
                            'type' => 'users',
                            'id' => '3',
                        ],
                    ];
                    return $resource;
                },
            ],
            'resource invalid links' => [
                false,
                function (array $resource) {
                    $resource['link'] = '/api/v1/foobars/123';
                    return $resource;
                },
            ],
            'resource with additional link' => [
                false,
                function (array $resource) {
                    $resource['links']['foo'] = '/api/v1/posts/1/foo';
                    return $resource;
                },
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedOneExactArrayProvider')]
    public function testFetchedOneExactWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->resource);

        if ($expected) {
            $this->http->assertFetchedOneExact($value);
        } else {
            $this->assertThatItFails(
                'member at [/data] exactly matches',
                fn() => $this->http->assertFetchedOneExact($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     */
     #[DataProvider('fetchedOneExactArrayProvider')]
    public function testFetchedOneExactWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->resource);

        if ($expected) {
            $this->http->assertFetchedOneExact(new TestObject($value));
            $this->http->assertFetchedOneExact(new Collection($value));
        } else {
            $this->assertThatItFails(
                'member at [/data] exactly matches',
                fn() => $this->http->assertFetchedOneExact(new TestObject($value))
            );

            $this->assertThatItFails(
                'member at [/data] exactly matches',
                fn() => $this->http->assertFetchedOneExact(new Collection($value))
            );
        }
    }

    public function testFetchedNull(): void
    {
        $http = $this->http->withContent('{"data": null}');

        $http->assertFetchedNull();

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedNull()
        );
    }

    public function testInvalidStatusCode(): void
    {
        $http = $this->http->withStatusCode(201);
        $null = $http->withContent('{"data": null}');

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedOne($this->resource)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedOneExact($this->resource)
        );

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $null->assertFetchedNull()
        );
    }

    public function testInvalidContentType(): void
    {
        $http = $this->http->withContentType('application/json');
        $null = $http->withContent('{"data": null}');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedOne($this->resource)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedOneExact($this->resource)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $null->assertFetchedNull()
        );
    }
}
