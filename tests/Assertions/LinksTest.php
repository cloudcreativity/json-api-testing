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

use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use CloudCreativity\JsonApi\Testing\Utils\JsonObject;

class LinksTest extends TestCase
{
    /**
     * @var array
     */
    private array $links = [
        'self' => '/api/v1/comments/123/relationships/post',
        'related' => '/api/v1/comments/123/post',
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
            json_encode(['data' => $this->post, 'links' => $this->links]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    public function testLinks(): void
    {
        $partial = $this->links;
        unset($partial['self']);

        $invalid = $this->links;
        $invalid['related'] = '/api/foo/bar';

        $this->http->assertLinks($this->links);
        $this->http->assertLinks(new TestObject($this->links));
        $this->http->assertLinks($partial);

        $this->assertThatItFails(
            'member at [/links] matches the subset',
            fn() => $this->http->assertLinks($invalid)
        );
    }

    public function testExactLinks(): void
    {
        $partial = $this->links;
        unset($partial['self']);

        $invalid = $this->links;
        $invalid['related'] = '/api/foo/bar';

        $this->http->assertExactLinks($this->links);
        $this->http->assertExactLinks(new JsonObject($this->links));

        $this->assertThatItFails(
            'member at [/links] exactly matches',
            fn() => $this->http->assertExactLinks($partial),
        );

        $this->assertThatItFails(
            'member at [/links] exactly matches',
            fn() => $this->http->assertExactLinks($invalid),
        );
    }

    public function testDoesntHaveLinks(): void
    {
        $none = $this->http->withContent(json_encode(['data' => $this->post]));
        $none->assertDoesntHaveLinks();

        $this->assertThatItFails(
            'Document has top-level links.',
            fn() => $this->http->assertDoesntHaveLinks()
        );
    }
}
