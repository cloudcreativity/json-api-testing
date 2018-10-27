<?php

namespace CloudCreativity\JsonApi\Testing;

class AssertErrorsTest extends TestCase
{

    /**
     * @var array
     */
    private $document;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->document = [
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
        ];
    }

    public function testNoErrors(): void
    {
        $document = ['data' => null];

        $this->willFail(function () use ($document) {
            Assert::assertError($document, ['status' => '422']);
        });

        $this->willFail(function () use ($document) {
            Assert::assertErrors($document, [['status' => '422']]);
        });

        $this->willFail(function () use ($document) {
            Assert::assertErrorsContains($document, ['status' => '422']);
        });
    }

    public function testOneError(): void
    {
        Assert::assertError($this->document, $error = [
            'status' => '422',
            'source' => [
                'pointer' => '/data/attributes/title',
            ],
        ]);

        Assert::assertErrors($this->document, [$error]);

        Assert::assertErrorsContains($this->document, $error);

        $this->willFail(function () {
            Assert::assertError($this->document, [
                'status' => '400',
            ]);
        });

        $this->willFail(function () use ($error) {
            Assert::assertErrors($this->document, [
                $error,
                ['status' => '400']
            ]);
        });
    }

    public function testMultipleErrors(): void
    {
        $this->document['errors'][] = [
            'title' => 'Bad Request',
            'status' => '400',
        ];

        Assert::assertErrors($this->document, [
            // order is not significant
            ['status' => '400'],
            $invalid = ['source' => ['pointer' => '/data/attributes/title']],
        ]);

        Assert::assertErrorsContains($this->document, $invalid);

        $this->willFail(function () use ($invalid) {
            // the error is present but it is not the only one.
            Assert::assertError($this->document, $invalid);
        });

        $this->willFail(function () {
            Assert::assertErrorsContains($this->document, ['status' => '500']);
        });

        $this->willFail(function () {
            $expected = $this->document['errors'];
            $expected[2] = ['status' => '500'];
            Assert::assertErrors($this->document, $expected);
        });
    }
}
