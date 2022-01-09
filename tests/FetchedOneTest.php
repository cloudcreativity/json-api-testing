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

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing;

use Closure;
use Illuminate\Contracts\Routing\UrlRoutable;

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

        $this->http->willSeeType($this->resource['type']);
    }

    public function testFetchOneWithUrlRoutable(): void
    {
        $model = $this->createMock(UrlRoutable::class);
        $model->method('getRouteKey')->willReturn((int) $this->resource['id']);

        $this->http->assertFetchedOne($model);
        $this->http->assertFetchedOne([
            'type' => $this->resource['type'],
            'id' => $model,
        ]);

        $invalid = $this->createMock(UrlRoutable::class);
        $invalid->method('getRouteKey')->willReturn($this->resource['id'] + 1);

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

    public function testFetchOneWithIntegerAndString(): void
    {
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
    public function fetchedOneArrayProvider(): array
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
     * @dataProvider fetchedOneArrayProvider
     */
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

    public function testFetchOneExactWithUrlRoutable(): void
    {
        $model = $this->createMock(UrlRoutable::class);
        $model->method('getRouteKey')->willReturn((int) $this->resource['id']);

        $this->assertThatItFails(
            'member at [/data] exactly matches',
            fn() => $this->http->assertFetchedOneExact($model)
        );
    }

    public function testFetchOneExactWithStringAndInteger(): void
    {
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
    public function fetchedOneExactArrayProvider(): array
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
     * @dataProvider fetchedOneExactArrayProvider
     */
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
