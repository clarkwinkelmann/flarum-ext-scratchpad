<?php

namespace ClarkWinkelmann\Scratchpad;

use Illuminate\Support\Str;

class PHPEvaluator
{
    public static function evaluate(string $php)
    {
        // Remove opening `<?php` token for eval()
        if (Str::startsWith($php, '<?php')) {
            $php = substr($php, 5);
        }

        // Convert errors and notices into exception so we can catch them before they break the responses
        // from https://www.php.net/manual/en/class.errorexception.php
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        $return = eval($php);

        restore_error_handler();

        return $return;
    }
}
