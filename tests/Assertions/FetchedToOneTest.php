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
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use Illuminate\Support\Collection;

class FetchedToOneTest extends TestCase
{

    /**
     * @var array
     */
    private array $identifier;

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

        $this->identifier = [
            'type' => 'posts',
            'id' => '1',
        ];

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['data' => $this->identifier]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testFetchedToOneWithUrlRoutable(): void
    {
        $this->http->willSeeType($this->identifier['type']);

        $model = new TestModel((int) $this->identifier['id']);

        $this->http->assertFetchedToOne($model);
        $this->http->assertFetchedToOne([
            'type' => 'posts',
            'id' => $model,
        ]);

        $invalid = new TestModel($this->identifier['id'] + 1);

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedToOne($invalid)
        );

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedToOne([
                'type' => $this->identifier['type'],
                'id' => $invalid,
            ])
        );
    }

    public function testFetchedToOneWithIntegerAndString(): void
    {
        $this->http->willSeeType($this->identifier['type']);

        $this->http->assertFetchedToOne($this->identifier['id']);
        $this->http->assertFetchedToOne((int) $this->identifier['id']);

        $invalid = intval($this->identifier['id']) + 1;

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedToOne((string) $invalid)
        );

        $this->assertThatItFails(
            'member at [/data] matches',
            fn() => $this->http->assertFetchedToOne($invalid)
        );
    }

    /**
     * @return array
     */
    public static function fetchedToOneArrayProvider(): array
    {
        return [
            'identifier' => [
                true,
                fn(array $identifier): array => $identifier,
            ],
            'invalid type' => [
                false,
                fn(array $identifier): array => [
                    'type' => 'foobar',
                    'id' => $identifier['id'],
                ],
            ],
            'invalid id' => [
                false,
                fn(array $identifier): array => [
                    'type' => $identifier['type'],
                    'id' => strval($identifier['id'] + 1),
                ],
            ],
        ];
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedToOneArrayProvider
     */
    public function testFetchedOneWithArray(bool $expected, Closure $provider): void
    {
        $value = $provider($this->identifier);

        if ($expected) {
            $this->http->assertFetchedToOne($value);
        } else {
            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedToOne($value)
            );
        }
    }

    /**
     * @param bool $expected
     * @param Closure $provider
     * @return void
     * @dataProvider fetchedToOneArrayProvider
     */
    public function testFetchedOneWithObject(bool $expected, Closure $provider): void
    {
        $value = $provider($this->identifier);

        if ($expected) {
            $this->http->assertFetchedToOne(new TestObject($value));
            $this->http->assertFetchedToOne(new Collection($value));
        } else {
            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedToOne(new TestObject($value))
            );

            $this->assertThatItFails(
                'member at [/data] matches',
                fn() => $this->http->assertFetchedToOne(new Collection($value))
            );
        }
    }

    /**
     * @return array[]
     */
    public static function resourceProvider(): array
    {
        return [
            'full' => [
                fn(array $identifier): array => [
                    'type' => $identifier['type'],
                    'id' => $identifier['id'],
                    'attributes' => [
                        'foo' => 'bar',
                    ],
                    'relationships' => [
                        'baz' => [
                            'data' => null,
                        ],
                    ],
                    'links' => [
                        'self' => sprintf('/api/v1/%s/%d', $identifier['type'], $identifier['id']),
                    ],
                ],
            ],
            'only attributes' => [
                fn(array $identifier): array => [
                    'type' => $identifier['type'],
                    'id' => $identifier['id'],
                    'attributes' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'only relationships' => [
                fn(array $identifier): array => [
                    'type' => $identifier['type'],
                    'id' => $identifier['id'],
                    'relationships' => [
                        'baz' => [
                            'data' => null,
                        ],
                    ],
                ],
            ],
            'only links' => [
                fn(array $identifier): array => [
                    'type' => $identifier['type'],
                    'id' => $identifier['id'],
                    'links' => [
                        'self' => sprintf('/api/v1/%s/%d', $identifier['type'], $identifier['id']),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param Closure $provider
     * @return void
     * @dataProvider resourceProvider
     */
    public function testFetchedToOneWithResource(Closure $provider): void
    {
        $value = $provider($this->identifier);
        $http = $this->http->withContent(json_encode(['data' => $value]));

        $this->assertThatItFails(
            'member at [/data] matches the resource identifier',
            fn() => $http->assertFetchedToOne($this->identifier),
        );
    }

    public function testInvalidStatusCode(): void
    {
        $http = $this->http->withStatusCode(201);

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertFetchedToOne($this->identifier)
        );
    }

    public function testInvalidContentType(): void
    {
        $http = $this->http->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertFetchedToOne($this->identifier)
        );
    }
}
