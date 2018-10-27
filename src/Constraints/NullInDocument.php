<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

class NullInDocument extends Constraint
{

    /**
     * @var string
     */
    private $pointer;

    /**
     * NullInDocument constructor.
     *
     * @param string $pointer
     *      the JSON pointer to the expected null value in the JSON API document.
     */
    public function __construct(string $pointer)
    {
        parent::__construct();
        $this->pointer = $pointer;
    }

    /**
     * @inheritdoc
     */
    public function matches($other): bool
    {
        $other = Document::cast($other);

        if (!$other->has($this->pointer)) {
            return false;
        }

        return null === $other->get($this->pointer);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return "{$this->pointer} is null";
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($document): string
    {
        return "the member at [{$this->pointer}] is null within JSON API document:" . PHP_EOL
            . Document::cast($document);
    }

}
