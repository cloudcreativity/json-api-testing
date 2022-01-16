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

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;

class MetaTest extends TestCase
{

    /**
     * @var array
     */
    private array $meta = [
        'foo' => 'bar',
        'baz' => 'bat',
        'page' => [
            'number' => 1,
            'size' => 10,
        ],
    ];

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

        $this->http = new HttpMessage(
            200,
            'application/vnd.api+json',
            json_encode(['meta' => $this->meta]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testMetaWithoutData(): void
    {
        $partial = $this->meta;
        unset($partial['baz']);

        $this->http->assertMetaWithoutData($this->meta);
        $this->http->assertMetaWithoutData($partial);

        $data = $this->http->withContent(json_encode([
            'data' => $this->post,
            'meta' => $this->meta,
        ]));

        $this->assertThatItFails(
            'Data member exists.',
            fn() => $data->assertMetaWithoutData($this->meta)
        );
    }

    public function testExactMetaWithoutData(): void
    {
        $partial = $this->meta;
        unset($partial['baz']);

        $this->http->assertExactMetaWithoutData($this->meta);

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $this->http->assertExactMetaWithoutData($partial),
        );

        $data = $this->http->withContent(json_encode([
            'data' => $this->post,
            'meta' => $this->meta,
        ]));

        $this->assertThatItFails(
            'Data member exists.',
            fn() => $data->assertExactMetaWithoutData($this->meta)
        );
    }

    public function testInvalidStatusCode(): void
    {
        $http = $this->http->withStatusCode(201);

        $this->assertThatItFails(
            'status 201 is 200',
            fn() => $http->assertMetaWithoutData($this->meta)
        );

        $this->assertThatItFails(
            'status 201 is 200',
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

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $this->http->assertExactMeta($partial),
        );

        $this->assertThatItFails(
            'member at [/meta] exactly matches',
            fn() => $this->http->assertExactMeta($invalid)
        );
    }
}
