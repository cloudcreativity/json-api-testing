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

class CreatedTest extends TestCase
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
            'id' => '7088eccc-a7cd-46f6-81f9-c553c9065dbd',
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
                'self' => 'http://localhost/api/v1/posts/7088eccc-a7cd-46f6-81f9-c553c9065dbd',
            ],
        ];

        $this->http = new HttpMessage(
            201,
            'application/vnd.api+json',
            json_encode(['data' => $this->resource]),
            [
                'Content-Type' => 'application/vnd.api+json',
                'Accept' => 'application/vnd.api+json',
                'Location' => $this->resource['links']['self'],
            ],
        );

        $this->http->willSeeType($this->resource['type']);
    }

    public function testWithServerId(): void
    {
        $this->http->assertCreatedWithServerId(
            'http://localhost/api/v1/posts',
            $this->dataForServerId()
        );
    }

    public function testWithServerIdUnexpectedStatusCode(): void
    {
        $http = $this->http->withStatusCode(200);

        $this->assertThatItFails(
            'status 200 is 201',
            fn() => $http->assertCreatedWithServerId(
                'http://localhost/api/v1/posts',
                $this->dataForServerId(),
            ),
        );
    }

    public function testWithServerIdMissingLocation(): void
    {
        $headers = $this->http->getHeaders();
        unset($headers['Location']);

        $http = $this->http->withHeaders($headers);

        $this->assertThatItFails(
            'Missing Location header',
            fn() => $http->assertCreatedWithServerId(
                'http://localhost/api/v1/posts',
                $this->dataForServerId(),
            ),
        );
    }

    public function testWithServerIdUnexpectedLocation(): void
    {
        $this->assertThatItFails(
            'Unexpected Location header',
            fn() => $this->http->assertCreatedWithServerId(
                'http://localhost/api/v1/tags',
                $this->dataForServerId()
            ),
        );
    }

    public function testWithServerIdDataDoesNotMatch(): void
    {
        $data = $this->dataForServerId();
        $data['attributes']['title'] = 'Unexpected';

        $this->assertThatItFails(
            'member at [/data] matches the subset',
            fn() => $this->http->assertCreatedWithServerId(
                'http://localhost/api/v1/posts',
                $data,
            ),
        );
    }

    public function testWithClientId(): void
    {
        $this->http->assertCreatedWithClientId(
            'http://localhost/api/v1/posts',
            $this->resource
        );
    }

    public function testWithClientIdUnexpectedStatusCode(): void
    {
        $http = $this->http->withStatusCode(200);

        $this->assertThatItFails(
            'status 200 is 201',
            fn() => $http->assertCreatedWithClientId(
                'http://localhost/api/v1/posts',
                $this->resource,
            ),
        );
    }

    public function testWithClientIdMissingLocation(): void
    {
        $headers = $this->http->getHeaders();
        unset($headers['Location']);

        $http = $this->http->withHeaders($headers);

        $this->assertThatItFails(
            'Missing Location header',
            fn() => $http->assertCreatedWithClientId(
                'http://localhost/api/v1/posts',
                $this->resource,
            ),
        );
    }

    public function testWithClientIdUnexpectedLocation(): void
    {
        $this->assertThatItFails(
            'Unexpected Location header',
            fn() => $this->http->assertCreatedWithClientId(
                'http://localhost/api/v1/tags',
                $this->resource,
            ),
        );
    }

    public function testWithClientIdDataDoesNotMatch(): void
    {
        $data = $this->resource;
        $data['attributes']['title'] = 'Unexpected';

        $this->assertThatItFails(
            'member at [/data] matches the subset',
            fn() => $this->http->assertCreatedWithClientId(
                'http://localhost/api/v1/posts',
                $data,
            ),
        );
    }

    public function testNoContent(): void
    {
        $this->noContent()->assertCreatedNoContent(
            $this->resource['links']['self']
        );
    }

    public function testNoContentUnexpectedStatusCode(): void
    {
        $http = $this->noContent()->withStatusCode(201);

        $this->assertThatItFails(
            'status 201 is 204',
            fn() => $http->assertCreatedNoContent(
                $this->resource['links']['self'],
            ),
        );
    }

    public function testNoContentMissingLocation(): void
    {
        $headers = $this->http->getHeaders();
        unset($headers['Location']);

        $http = $this->noContent()->withHeaders($headers);

        $this->assertThatItFails(
            'Missing Location header',
            fn() => $http->assertCreatedNoContent(
                $this->resource['links']['self']
            ),
        );
    }

    public function testNoContentUnexpectedLocation(): void
    {
        $this->assertThatItFails(
            'Unexpected Location header',
            fn() => $this->noContent()->assertCreatedNoContent(
                'http://localhost/api/v1/posts/ccb4556f-39ee-4c22-babe-05b7cce8b900',
            ),
        );
    }

    /**
     * @return array
     */
    private function dataForServerId(): array
    {
        $data = $this->resource;
        unset($data['id'], $data['links']);

        return $data;
    }

    /**
     * @return HttpMessage
     */
    private function noContent(): HttpMessage
    {
        return $this->http
            ->withStatusCode(204)
            ->withContent(null);
    }
}
