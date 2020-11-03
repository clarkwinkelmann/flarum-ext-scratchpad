<?php

namespace ClarkWinkelmann\Scratchpad\ErrorHandling;

use Flarum\Foundation\ErrorHandling\HandledError;

class ValidationExceptionWithMetaHandler
{
    public function handle(ValidationExceptionWithMeta $exception)
    {
        return (new HandledError(
            $exception,
            'validation_error',
            422
        ))->withDetails([
            [
                'detail' => $exception->detail,
                'source' => [
                    'pointer' => '/data/attributes/' . $exception->attribute,
                ],
                'meta' => $exception->meta,
            ],
        ]);
    }
}
