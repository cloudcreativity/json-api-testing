<?php

namespace CloudCreativity\JsonApi\Testing\Constraints;

use CloudCreativity\JsonApi\Testing\Document;
use PHPUnit\Framework\Constraint\Constraint;

class HttpStatusIs extends Constraint
{

    /**
     * @var int
     */
    private $expected;

    /**
     * @var string|null
     */
    private $content;

    /**
     * HttpStatusIs constructor.
     *
     * @param int $expected
     * @param string|null $content
     */
    public function __construct(int $expected, $content = null)
    {
        parent::__construct();
        $this->expected = $expected;
        $this->content = $content;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return (string) $this->expected;
    }

    /**
     * @inheritdoc
     */
    protected function failureDescription($other): string
    {
        $message = "the HTTP status {$other} is {$this->toString()}";
        $document = Document::create($this->content);
        
        if ($document && $document->has('errors')) {
            return $message . ". The response errors were:"  . PHP_EOL . $document;
        }

        return $message;
    }

    /**
     * @param mixed $other
     * @return bool
     */
    protected function matches($other): bool
    {
        if (!is_numeric($other)) {
            return false;
        }

        return $this->expected === (int) $other;
    }

}
