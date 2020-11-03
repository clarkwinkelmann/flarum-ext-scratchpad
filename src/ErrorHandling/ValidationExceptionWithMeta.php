<?php

namespace ClarkWinkelmann\Scratchpad\ErrorHandling;

use Exception;

class ValidationExceptionWithMeta extends Exception
{
    public $attribute;
    public $detail;
    public $meta;

    public function __construct(string $attribute, string $detail, array $meta)
    {
        $this->attribute = $attribute;
        $this->detail = $detail;
        $this->meta = $meta;

        parent::__construct($detail);
    }
}
