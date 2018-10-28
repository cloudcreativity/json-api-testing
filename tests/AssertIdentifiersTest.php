<?php

namespace CloudCreativity\JsonApi\Testing;

class AssertIdentifiersTest extends TestCase
{

    public function testExactArray(): Document
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
                ],
            ],
        ]);

        $expected = $document['data'];
        $unordered = collect($expected)->reverse()->values()->all();

        $document->assertExactArray($expected)
            ->assertExactArray($unordered)
            ->assertArray($expected)
            ->assertArray($unordered)
            ->assertArrayNotEmpty();

        $this->willFail(function () use ($document, $expected) {
            $expected[] = ['type' => 'comments', 'id' => '3'];
            $document->assertExactArray($expected);
        }, 'additional identifier');

        $this->willFail(function () use ($document, $expected) {
            $document->assertArrayEmpty();
        }, 'empty array');

        return $document;
    }

    /**
     * @param Document $document
     * @depends testExactArray
     */
    public function testExactArrayInOrder(Document $document): void
    {
        $document->assertExactArrayInOrder($document['data'])
            ->assertArrayInOrder($document['data']);

        $this->willFail(function () use ($document) {
            $document->assertExactArrayInOrder(
                collect($document['data'])->reverse()->values()->all()
            );
        });
    }

    /**
     * @param Document $document
     * @depends testExactArray
     */
    public function testContainsExact(Document $document): void
    {
        $document->assertArrayContainsExact(
            ['type' => 'comments', 'id' => '2']
        );

        $this->willFail(function () use ($document) {
            $document->assertArrayContainsExact(
                ['type' => 'comments', 'id' => '3']
            );
        });
    }

    public function testEmpty(): void
    {
        $document = new Document(['data' => []]);

        $document->assertArrayEmpty();

        $this->willFail(function () use ($document) {
            $document->assertArrayNotEmpty();
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
            $document->assertExactArray($expected);
        }, 'exact array');

        $this->willFail(function () use ($document, $expected) {
            $document->assertExactArrayInOrder($expected);
        }, 'exact array in order');


        $this->willFail(function () use ($document) {
            $document->assertArrayContainsExact(
                ['type' => 'comments', 'id' => '2']
            );
        }, 'contains exact');
    }
}
