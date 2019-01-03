<?php
/**
 * Copyright 2019 Cloud Creativity Limited
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

class AssertErrorsTest extends TestCase
{

    /**
     * @var Document
     */
    private $single;

    /**
     * @var Document
     */
    private $multiple;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->single = new Document($document = [
            'errors' => [
                [
                    'title' => 'Invalid',
                    'detail' => 'The title attribute must be a string.',
                    'status' => '422',
                    'source' => [
                        'pointer' => '/data/attributes/title',
                    ],
                ],
            ],
        ]);

        $document['errors'][] = ['title' => 'Bad Request', 'status' => '400'];
        $this->multiple = new Document($document);
    }

    public function testNoErrors(): void
    {
        $document = new Document(['data' => null]);

        $this->willFail(function () use ($document) {
            $document->assertError(['status' => '422']);
        });

        $this->willFail(function () use ($document) {
            $document->assertErrors([['status' => '422']]);
        });

        $this->willFail(function () use ($document) {
            $document->assertHasError(['status' => '422']);
        });
    }

    public function testOneError(): void
    {
        $this->single->assertError($error = [
            'status' => '422',
            'source' => [
                'pointer' => '/data/attributes/title',
            ],
        ]);

        $this->single->assertErrors([$error]);

        $this->single->assertHasError($error);

        $this->willFail(function () {
            $this->single->assertError([
                'status' => '400',
            ]);
        });

        $this->willFail(function () use ($error) {
            $this->single->assertErrors([
                $error,
                ['status' => '400']
            ]);
        });
    }

    public function testMultipleErrors(): void
    {
        $this->multiple->assertErrors([
            // order is not significant
            ['status' => '400'],
            $invalid = ['source' => ['pointer' => '/data/attributes/title']],
        ]);

        $this->multiple->assertHasError($invalid);

        $this->willFail(function () use ($invalid) {
            // the error is present but it is not the only one.
            $this->multiple->assertError($invalid);
        });

        $this->willFail(function () {
            $this->multiple->assertHasError(['status' => '500']);
        });

        $this->willFail(function () {
            $expected = $this->single['errors'];
            $expected[2] = ['status' => '500'];
            $this->multiple->assertErrors($expected);
        });
    }
}
