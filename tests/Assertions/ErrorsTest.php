<?php
/*
 * Copyright 2023 Cloud Creativity Limited
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

declare(strict_types=1);

namespace CloudCreativity\JsonApi\Testing\Tests\Assertions;

use CloudCreativity\JsonApi\Testing\HttpMessage;
use CloudCreativity\JsonApi\Testing\Tests\TestCase;
use CloudCreativity\JsonApi\Testing\Tests\TestObject;
use Illuminate\Support\Collection;

class ErrorsTest extends TestCase
{
    /**
     * @var array
     */
    private array $error422 = [
        'detail' => 'Expecting a string.',
        'source' => ['pointer' => '/data/attributes/title'],
        'status' => '422',
        'title' => 'Unprocessable Entity',
    ];

    /**
     * @var array
     */
    private array $error400 = [
        'detail' => 'Unexpected filter "foo".',
        'source' => ['parameter' => 'filter.foo'],
        'status' => '400',
        'title' => 'Bad Request',
    ];

    public function testErrorStatus(): void
    {
        $http = $this->createError(422, $this->error422);
        $http->assertErrorStatus($this->error422);
        $http->assertErrorStatus(new TestObject($this->error422));

        $partial = $this->error422;
        unset($partial['source']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Something went wrong.';

        $http->assertErrorStatus($partial);

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->withStatusCode(400)->assertErrorStatus($this->error422),
        );

        $this->assertThatItFails(
            'status 422 is 400',
            fn() => $http->assertErrorStatus(new TestObject($this->error400)),
        );

        $this->assertThatItFails(
            'array at [/errors] only contains the subsets',
            fn() => $http->assertErrorStatus($invalid),
        );
    }

    public function testExactErrorStatus(): void
    {
        $http = $this->createError(422, $this->error422);
        $http->assertExactErrorStatus($this->error422);
        $http->assertExactErrorStatus(new TestObject($this->error422));

        $partial = $this->error422;
        unset($partial['source']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Something went wrong.';

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactErrorStatus($partial),
        );

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->withStatusCode(400)->assertExactErrorStatus($this->error422),
        );

        $this->assertThatItFails(
            'status 422 is 400',
            fn() => $http->assertExactErrorStatus(new TestObject($this->error400)),
        );

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactErrorStatus($invalid),
        );
    }

    public function testErrorStatusInvalidContentType(): void
    {
        $http = $this
            ->createError(400, $this->error400)
            ->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertErrorStatus($this->error400),
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertExactErrorStatus($this->error400),
        );
    }

    public function testError(): void
    {
        $http = $this->createError(400, $this->error422);
        $http->assertError(400, $this->error422);
        $http->assertError(400, new TestObject($this->error422));

        $partial = $this->error422;
        unset($partial['source']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Something went wrong.';

        $http->assertError(400, $partial);

        $this->assertThatItFails(
            'status 422 is 400',
            fn() => $http->withStatusCode(422)->assertError(400, $this->error422),
        );

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->assertError(422, new TestObject($this->error400)),
        );

        $this->assertThatItFails(
            'array at [/errors] only contains the subsets',
            fn() => $http->assertError(400, $invalid),
        );
    }

    public function testExactError(): void
    {
        $http = $this->createError(400, $this->error422);
        $http->assertExactError(400, $this->error422);
        $http->assertExactError(400, new TestObject($this->error422));

        $partial = $this->error422;
        unset($partial['source']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Something went wrong.';

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactError(400, $partial),
        );

        $this->assertThatItFails(
            'status 422 is 400',
            fn() => $http->withStatusCode(422)->assertExactError(400, $this->error422),
        );

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->assertExactError(422, new TestObject($this->error400)),
        );

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactError(400, $invalid),
        );
    }

    public function testErrorInvalidContentType(): void
    {
        $http = $this
            ->createError(400, $this->error422)
            ->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertError(400, $this->error422),
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertExactError(400, $this->error422),
        );
    }

    public function testErrors(): void
    {
        $http = $this->createErrors();
        $expected = [$this->error400, $this->error422]; // order not significant.

        $partial = $this->error400;
        unset($partial['detail']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Oops! Something went wrong.';

        $http->assertErrors(400, $expected);
        $http->assertErrors(400, new Collection($expected));
        $http->assertErrors(400, [$partial, $this->error422]);

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->assertErrors(422, $expected),
        );

        $this->assertThatItFails(
            'array at [/errors] only contains the subsets',
            fn() => $http->assertErrors(400, [$this->error400, $invalid]),
        );
    }

    public function testExactErrors(): void
    {
        $http = $this->createErrors();
        $expected = [$this->error400, $this->error422]; // order not significant.

        $partial = $this->error400;
        unset($partial['detail']);

        $invalid = $this->error422;
        $invalid['detail'] = 'Oops! Something went wrong.';

        $http->assertExactErrors(400, $expected);
        $http->assertExactErrors(400, new Collection($expected));

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactErrors(400, [$partial, $this->error422]),
        );

        $this->assertThatItFails(
            'status 400 is 422',
            fn() => $http->assertExactErrors(422, $expected),
        );

        $this->assertThatItFails(
            'list at [/errors] only contains the values',
            fn() => $http->assertExactErrors(400, [$this->error400, $invalid]),
        );
    }

    public function testErrorsInvalidContentType(): void
    {
        $expected = [$this->error422, $this->error400];

        $http = $this
            ->createErrors()
            ->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertErrors(400, $expected),
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertExactErrors(400, $expected),
        );
    }

    public function testHasError(): void
    {
        $http = $this->createErrors();

        $http->assertHasError(400, $this->error422);
        $http->assertHasError(400, new TestObject($this->error400));

        $partial = $this->error422;
        unset($partial['detail']);

        $http->assertHasError(400, $partial);

        $invalid = $this->error422;
        $invalid['detail'] = 'Blah!';

        $this->assertThatItFails(
            'the array at [/errors] contains the subset',
            fn() => $http->assertHasError(400, $invalid),
        );

        $this->assertThatItFails(
            '422 is 400',
            fn() => $http->withStatusCode(422)->assertHasError(400, $this->error400),
        );
    }

    public function testHasExactError(): void
    {
        $http = $this->createErrors();

        $http->assertHasExactError(400, $this->error422);
        $http->assertHasExactError(400, new TestObject($this->error400));

        $partial = $this->error422;
        unset($partial['detail']);


        $this->assertThatItFails(
            'the list at [/errors] contains the values',
            fn() => $http->assertHasExactError(400, $partial),
        );

        $invalid = $this->error422;
        $invalid['detail'] = 'Blah!';

        $this->assertThatItFails(
            'the list at [/errors] contains the values',
            fn() => $http->assertHasExactError(400, $invalid),
        );

        $this->assertThatItFails(
            '422 is 400',
            fn() => $http->withStatusCode(422)->assertHasExactError(400, $this->error400),
        );
    }

    public function testHasErrorInvalidContentType(): void
    {
        $http = $this
            ->createErrors()
            ->withContentType('application/json');

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertHasError(400, $this->error400),
        );

        $this->assertThatItFails(
            'media type',
            fn() => $http->assertHasExactError(400, $this->error422),
        );
    }

    /**
     * @param int $status
     * @param array $error
     * @return HttpMessage
     */
    private function createError(int $status, array $error): HttpMessage
    {
        return new HttpMessage(
            $status,
            'application/vnd.api+json',
            json_encode(['errors' => [$error]]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }

    /**
     * @return HttpMessage
     */
    private function createErrors(): HttpMessage
    {
        return new HttpMessage(
            400,
            'application/vnd.api+json',
            json_encode(['errors' => [$this->error422, $this->error400]]),
            ['Content-Type' => 'application/vnd.api+json', 'Accept' => 'application/vnd.api+json'],
        );
    }
}
