<?php

namespace ClarkWinkelmann\Scratchpad\ErrorHandling;

use Exception;
use Flarum\Foundation\KnownError;

class InvalidLiveTokenException extends Exception implements KnownError
{
    public function getType(): string
    {
        return 'permission_denied';
    }
}
