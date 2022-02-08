<?php
/*
 * Copyright 2022 Cloud Creativity Limited
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

namespace CloudCreativity\JsonApi\Testing\Tests;

use Illuminate\Contracts\Routing\UrlRoutable;
use JsonSerializable;

class TestModel implements UrlRoutable, JsonSerializable
{
    /**
     * @var string|int
     */
    private $id;

    /**
     * TestModel constructor.
     *
     * @param string|int $id
     */
    public function __construct($id)
    {
        if (!is_string($id) && !is_int($id)) {
            throw new \InvalidArgumentException('Expecting a string or integer id.');
        }

        $this->id = $id;
    }

    /**
     * @inheritDoc
     */
    public function getRouteKey()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public function resolveRouteBinding($value, $field = null)
    {
        throw new \LogicException('Not implemented.');
    }

    /**
     * @inheritDoc
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        throw new \LogicException('Not implemented.');
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        /**
         * An Eloquent model would return a serialization that we would not expect to compare to JSON:API
         * document values. So for test purposes, we return junk here to ensure that the model JSON
         * representation is not used. Instead, we expect `getRouteKey()` to be called and that value
         * combined with an expected resource type to form the expected JSON:API resource identifier.
         */
        return ['foo' => 'bar'];
    }
}