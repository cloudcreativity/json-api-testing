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

namespace CloudCreativity\JsonApi\Testing\Utils;

use JsonException;
use RuntimeException;
use function is_array;
use function json_decode;
use function json_encode;

trait JsonToArray
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        try {
            $value = json_decode(json_encode($this), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            throw new RuntimeException('Encountered an error converting JSON value to an array.', 0, $ex);
        }

        if (is_array($value)) {
            return $value;
        }

        throw new RuntimeException('JSON value does not decode as an array.');
    }
}
