<?php

/**
 * Copyright 2017 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CloudCreativity\JsonApi\Testing;

/**
 * Class AssertIncludedTest
 *
 * @package CloudCreativity\JsonApi\Testing
 */
class AssertIncludedTest extends TestCase
{

    /**
     * @var Document
     */
    private $document;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->document = new Document([
            'data' => [
                'type' => 'posts',
                'id' => '123',
                'relationships' => [
                    'comments' => [
                        ['type' => 'comments', 'id' => '1'],
                        ['type' => 'comments', 'id' => '2'],
                    ],
                ],
            ],
            'included' => [
                [
                    'type' => 'comments',
                    'id' => '1',
                    'attributes' => [
                        'content' => 'My first comment',
                    ],
                ],
                [
                    'type' => 'comments',
                    'id' => '2',
                    'attributes' => [
                        'content' => 'My second comment',
                    ],
                ],
            ],
        ]);
    }

    public function testIncludedContains(): void
    {
        $this->document->assertIncludedContains('comments', '2');

        $this->willFail(function () {
            $this->document->assertIncludedContains('comments', '99');
        });
    }

    public function testIncludedContainsSubset(): void
    {
        $this->document->assertIncludedContainsSubset([
            'type' => 'comments',
            'id' => '1',
            'attributes' => [
                'content' => 'My first comment',
            ],
        ]);

        $this->willFail(function () {
            $this->document->assertIncludedContainsSubset([
                'type' => 'comments',
                'id' => '3',
                'attributes' => [
                    'content' => 'My third comment',
                ],
            ]);
        });
    }

    public function testIncluded(): void
    {
        $unordered = collect($this->document['included'])->reverse()->all();

        $this->document->assertIncluded($unordered);

        $unexpected = $unordered;
        $unexpected[2] = ['type' => 'comments', 'id' => '3'];

        $this->willFail(function () use ($unexpected) {
            $this->document->assertIncluded($unexpected);
        });
    }
}
