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

use Countable;
use Generator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;
use function is_array;
use function iterator_to_array;
use function json_encode;

final class JsonStack implements IteratorAggregate, Countable, JsonSerializable, Jsonable, Arrayable
{
    use JsonToArray;

    /**
     * @var iterable
     */
    private iterable $stack;

    /**
     * @var string
     */
    private string $type;

    /**
     * Cast a value to a JSON stack.
     *
     * @param iterable $stack
     * @param string $type
     * @return self
     */
    public static function cast(iterable $stack, string $type = ''): self
    {
        if ($stack instanceof self) {
            return $stack;
        }

        return new self($stack, $type);
    }

    /**
     * JsonStack constructor.
     *
     * @param iterable $stack
     * @param string $type
     */
    public function __construct(iterable $stack, string $type = '')
    {
        $this->stack = $stack;
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->cursor();
    }

    /**
     * @return Generator
     */
    public function cursor(): Generator
    {
        foreach ($this->stack as $value) {
            yield JsonObject::cast($value, $this->type);
        }
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return iterator_to_array($this);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        if ($this->stack instanceof Countable || is_array($this->stack)) {
            return count($this->stack);
        }

        return count($this->all());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @inheritDoc
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->all();
    }
}
