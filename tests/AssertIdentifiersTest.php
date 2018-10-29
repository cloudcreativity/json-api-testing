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

        $document->assertExactList($expected)
            ->assertExactList($unordered)
            ->assertList($expected)
            ->assertList($unordered)
            ->assertListNotEmpty();

        $this->willFail(function () use ($document, $expected) {
            $expected[] = ['type' => 'comments', 'id' => '3'];
            $document->assertExactList($expected);
        }, 'additional identifier');

        $this->willFail(function () use ($document, $expected) {
            $document->assertListEmpty();
        }, 'empty array');

        return $document;
    }

    /**
     * @param Document $document
     * @depends testExactArray
     */
    public function testExactArrayInOrder(Document $document): void
    {
        $document->assertExactListInOrder($document['data'])
            ->assertListInOrder($document['data']);

        $this->willFail(function () use ($document) {
            $document->assertExactListInOrder(
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
        $document->assertListContainsExact(
            ['type' => 'comments', 'id' => '2']
        );

        $this->willFail(function () use ($document) {
            $document->assertListContainsExact(
                ['type' => 'comments', 'id' => '3']
            );
        });
    }

    public function testEmpty(): void
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
            $document->assertExactList($expected);
        }, 'exact array');

        $this->willFail(function () use ($document, $expected) {
            $document->assertExactListInOrder($expected);
        }, 'exact array in order');


        $this->willFail(function () use ($document) {
            $document->assertListContainsExact(
                ['type' => 'comments', 'id' => '2']
            );
        }, 'contains exact');
    }
}
