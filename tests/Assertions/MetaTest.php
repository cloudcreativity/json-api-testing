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
use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use PHPUnit\Framework\Attributes\DataProvider;

class MetaTest extends TestCase
{

    /**
     * @var array
     */
    private array $meta;

    /**
     * @var array
     */
    private array $post = [
        'type' => 'posts',
        'id' => '1',
        'attributes' => [
            'title' => 'Hello World',
            'content' => '...',
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

        $this->meta = [
            'foo' => 'bar',
            'baz' => 'bat',
            'since' => Carbon::yesterday(),
            'page' => [
                'number' => 1,
                'size' => 10,
            ],
        ];

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['meta' => $this->meta]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    /**
     * @return array
     */
    public static function statusCodeProvider(): array
    {
        return [
            '200' => [200],
            '201' => [201],
            '202' => [202],
            '299' => [299],
        ];
    }

    /**
     * @param int $status
     * @return void
     */
    #[DataProvider('statusCodeProvider')]
    public function testMetaWithoutData(int $status): void
    {
        $http = $this->http->withStatusCode($status);

        $partial = $this->meta;
        unset($partial['baz']);

        $http->assertMetaWithoutData($this->meta);
        $http->assertMetaWithoutData($partial);
        $http->assertMetaWithoutData(new TestObject($this->meta));

        $data = $http->withContent(json_encode([
            'data' => $this->post,
            'meta' => $this->meta,
        ]));

        $this->assertThatItFails(
            'Data member exists.',
            fn() => $data->assertMetaWithoutData($this->meta)
        );
    }

    /**
     * @param int $status
     * @return void
     */
    #[DataProvider('statusCodeProvider')]
    public function testExactMetaWithoutData(int $status): void
    {
        $http = $this->http->withStatusCode($status);

        $partial = $this->meta;
        unset($partial['baz']);

        $http->assertExactMetaWithoutData($this->meta);
        $http->assertExactMetaWithoutData(new TestObject($this->meta));

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $http->assertExactMetaWithoutData($partial),
        );

        $data = $http->withContent(json_encode([
            'data' => $this->post,
            'meta' => $this->meta,
        ]));

        $this->assertThatItFails(
            'Data member exists.',
            fn() => $data->assertExactMetaWithoutData($this->meta)
        );
    }

    /**
     * @return array
     */
    public static function invalidStatusCodeProvider(): array
    {
        return [
            '100' => [100],
            '199' => [199],
            // 204 is invalid as the response has content.
            '204' => [204, 'HTTP status 204 No Content is invalid as there is content'],
            '300' => [300],
            '399' => [399],
            '400' => [400],
            '499' => [499],
            '500' => [500],
            '599' => [599],
        ];
    }

    /**
     * @param int $status
     * @param string $expected
     * @return void
     */
    #[DataProvider('invalidStatusCodeProvider')]
    public function testInvalidStatusCode(int $status, string $expected = ''): void
    {
        $expected = $expected ?: "HTTP status {$status} is successful";

        $http = $this->http->withStatusCode($status);

        $this->assertThatItFails(
            $expected,
            fn() => $http->assertMetaWithoutData($this->meta)
        );

        $this->assertThatItFails(
            $expected,
            fn() => $http->assertExactMetaWithoutData($this->meta)
        );
    }

    public function testInvalidContentType(): void
    {
        $http = $this->http->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertMetaWithoutData($this->meta)
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertExactMetaWithoutData($this->meta)
        );
    }

    public function testMeta(): void
    {
        $partial = $this->meta;
        unset($partial['baz']);

        $invalid = $this->meta;
        $invalid['page']['number'] = 99;

        $this->http->assertMeta($this->meta);
        $this->http->assertMeta(new TestObject($this->meta));
        $this->http->assertMeta($partial);

        $this->assertThatItFails(
            'member at [/meta] matches the subset',
            fn() => $this->http->assertMeta($invalid)
        );
    }

    public function testExactMeta(): void
    {
        $partial = $this->meta;
        unset($partial['baz']);

        $invalid = $this->meta;
        $invalid['page']['number'] = 99;

        $this->http->assertExactMeta($this->meta);
        $this->http->assertExactMeta(new TestObject($this->meta));

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $this->http->assertExactMeta($partial),
        );

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $this->http->assertExactMeta($invalid)
        );
    }

    public function testDoesntHaveMeta(): void
    {
        $none = $this->http->withContent(json_encode(['data' => $this->post]));

        $none->assertDoesntHaveMeta();

        $this->assertThatItFails(
            'Document has top-level meta.',
            fn() => $this->http->assertDoesntHaveMeta()
        );
    }
}
