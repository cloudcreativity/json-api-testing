<?php
/*
 * Copyright 2021 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing\Tests;

use CloudCreativity\JsonApi\Testing\Document;

class AssertErrorsTest extends TestCase
{

    /**
     * @var Document
     */
    private $single;

    /**
     * @var array
     */
    private $error422;

    /**
     * @var array
     */
    private $error400;

    /**
     * @var Document
     */
    private $multiple;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->single = new Document($document = [
            'errors' => [
                $this->error422 = [
                    'title' => 'Invalid',
                    'detail' => 'The title attribute must be a string.',
                    'status' => '422',
                    'source' => [
                        'pointer' => '/data/attributes/title',
                    ],
                ],
            ],
        ]);

        $document['errors'][] = $this->error400 = ['title' => 'Bad Request', 'status' => '400'];
        $this->multiple = new Document($document);
    }

    public function testNoErrors(): void
    {
        $document = new Document(['data' => null]);

        $this->willFail(function () use ($document) {
            $document->assertError(['status' => '422']);
        });

        $this->willFail(function () use ($document) {
            $document->assertExactError($this->error422);
        });

        $this->willFail(function () use ($document) {
            $document->assertErrors([['status' => '422']]);
        });

        $this->willFail(function () use ($document) {
            $document->assertExactErrors([$this->error422]);
        });

        $this->willFail(function () use ($document) {
            $document->assertHasError(['status' => '422']);
        });

        $this->willFail(function () use ($document) {
            $document->assertHasExactError($this->error422);
        });
    }

    public function testOneError(): void
    {
        $this->single->assertError($subset = [
            'status' => '422',
            'source' => [
                'pointer' => '/data/attributes/title',
            ],
        ]);

        $incorrect = ['status' => '400'];

        $this->single->assertError($subset);
        $this->single->assertExactError($this->error422);

        $this->single->assertErrors([$subset]);
        $this->single->assertExactErrors([$this->error422]);

        $this->single->assertHasError($subset);
        $this->single->assertHasExactError($this->error422);

        $this->willFail(function () use ($incorrect) {
            $this->single->assertError($incorrect);
        });

        $this->willFail(function () use ($subset) {
            $this->single->assertExactError($subset);
        });

        $this->willFail(function () use ($subset, $incorrect) {
            $this->single->assertErrors([$subset, $incorrect]);
        });

        $this->willFail(function () use ($subset) {
            $this->single->assertExactErrors([$subset]);
        });

        $this->willFail(function () use ($incorrect) {
            $this->single->assertHasError($incorrect);
        });

        $this->willFail(function () use ($subset) {
            $this->single->assertHasExactError($subset);
        });
    }

    public function testMultipleErrors(): void
    {
        $other = ['status' => '500'];

        /** Multiple Errors */
        $this->multiple->assertErrors([
            // order is not significant
            $subset400 = ['status' => '400'],
            $subset422 = ['source' => ['pointer' => '/data/attributes/title']],
        ]);

        $this->multiple->assertExactErrors([
            // order is not significant
            $this->error400,
            $this->error422
        ]);

        $this->willFail(function () use ($other, $subset400, $subset422) {
            $this->multiple->assertErrors([
                $subset400,
                $subset422,
                $other,
            ]);
        });

        $this->willFail(function () use ($other) {
            $this->multiple->assertExactErrors([
                $this->error400,
                $this->error422,
                $other
            ]);
        });

        /** Has Error */
        $this->multiple->assertHasError($subset422);
        $this->multiple->assertHasExactError($this->error400);

        $this->willFail(function () use ($other) {
            $this->multiple->assertHasError($other);
        });

        $this->willFail(function () use ($subset422) {
            $this->multiple->assertHasExactError($subset422);
        });

        /** Single Error */
        $this->willFail(function () use ($subset422) {
            // the error is present but it is not the only one.
            $this->multiple->assertError($subset422);
        });

        $this->willFail(function () {
            $this->multiple->assertExactError($this->error422);
        });
    }
}
