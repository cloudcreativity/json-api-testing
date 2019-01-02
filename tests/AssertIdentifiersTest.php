<?php

namespace CloudCreativity\JsonApi\Testing;

class AssertIdentifiersTest extends TestCase
{

    public function testIdentifier()
    {
        $document = new Document([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'meta' => [
                    'foo' => 'bar',
                ],
            ],
        ]);

        $document->assertIdentifier('comments', '1');

        $this->willFail(function () use ($document) {
            $document->assertIdentifier('comments', '2');
        });
    }

    public function testResourceWithAttributes()
    {
        $document = new Document([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'attributes' => [
                    'content' => 'Hello world',
                ],
            ],
        ]);

        $this->willFail(function () use ($document) {
            $document->assertIdentifier('comments', '1');
        });
    }

    public function testResourceWithRelationships()
    {
        $document = new Document([
            'data' => [
                'type' => 'comments',
                'id' => '1',
                'relationships' => [
                    'post' => [
                        'data' => [
                            'type' => 'posts',
                            'id' => '2',
                        ],
                    ],
                ],
            ],
        ]);

        $this->willFail(function () use ($document) {
            $document->assertIdentifier('comments', '1');
        });
    }

    public function testIdentifiersList(): Document
    {
        $document = new Document([
            'data' => [
                [
                    'type' => 'comments',
                    'id' => '1',
                ],
                [
                    'type' => 'comments',
                    'id' => '2',
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ]);

        $expected = $document['data'];
        $unordered = collect($expected)->reverse()->values()->all();

        $document->assertIdentifiersList($expected)
            ->assertIdentifiersList($unordered)
            ->assertListNotEmpty();

        $this->willFail(function () use ($document, $expected) {
            $expected[] = ['type' => 'comments', 'id' => '3'];
            $document->assertIdentifiersList($expected);
        }, 'additional identifier');

        $this->willFail(function () use ($document, $expected) {
            $document->assertListEmpty();
        }, 'empty array');

        return $document;
    }

    /**
     * @param Document $document
     * @depends testIdentifiersList
     */
    public function testIdentifiersListInOrder(Document $document): void
    {
        $document->assertIdentifiersListInOrder($document['data']);

        $this->willFail(function () use ($document) {
            $document->assertIdentifiersListInOrder(
                collect($document['data'])->reverse()->values()->all()
            );
        });
    }

    /**
     * @param Document $document
     * @depends testIdentifiersList
     */
    public function testContainsIdentifier(Document $document): void
    {
        $document->assertListContainsIdentifier('comments', '2');

        $this->willFail(function () use ($document) {
            $document->assertListContainsIdentifier('comments', '3');
        });
    }

    public function testEmptyList(): void
    {
        $document = new Document(['data' => []]);

        $document->assertListEmpty();

        $this->willFail(function () use ($document) {
            $document->assertListNotEmpty();
        });
    }

    public function testResourceObjects(): void
    {
        $document = new Document([
            'data' => [
                [
                    'type' => 'comments',
                    'id' => '1',
                    'attributes' => [
                        'content' => '...',
                    ],
                ],
                [
                    'type' => 'comments',
                    'id' => '2',
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id' => '123',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $expected = [
            [
                'type' => 'comments',
                'id' => '1',
            ],
            [
                'type' => 'comments',
                'id' => '2',
            ],
        ];

        $this->willFail(function () use ($document, $expected) {
            $document->assertIdentifiersList($expected);
        }, 'exact array');

        $this->willFail(function () use ($document, $expected) {
            $document->assertIdentifiersListInOrder($expected);
        }, 'exact array in order');

        $this->willFail(function () use ($document) {
            $document->assertListContainsIdentifier('comments', '2');
        });
    }
}
