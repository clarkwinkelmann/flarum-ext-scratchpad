<?php

namespace ClarkWinkelmann\Scratchpad;

use Psr\Log\LoggerInterface;

class PHPLoadErrors
{
    public static $errors = [];

    public static function record(Scratchpad $scratchpad, \Throwable $exception)
    {
        self::$errors[] = 'Failed to load scratchpad ' . $scratchpad->title . '! ' . get_class($exception) . ': ' . $exception->getMessage();

        /**
         * @var $logger LoggerInterface
         */
        $logger = resolve(LoggerInterface::class);
        $logger->error($exception);
    }
}
