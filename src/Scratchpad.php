<?php

namespace ClarkWinkelmann\Scratchpad;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property boolean $enabled
 * @property string $title
 * @property string $admin_js
 * @property string $forum_js
 * @property string $admin_js_compiled
 * @property string $forum_js_compiled
 * @property string $admin_less
 * @property string $forum_less
 * @property string $php
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Scratchpad extends AbstractModel
{
    public $timestamps = true;

    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected $visible = [
        'enabled',
        'title',
        'admin_js',
        'forum_js',
        'admin_less',
        'forum_less',
        'php',
    ];

    public function phpForEval(): string
    {
        $php = $this->php;

        // Remove opening `<?php` token for eval()
        if (Str::startsWith($php, '<?php')) {
            $php = substr($php, 5);
        }

        return $php;
    }

    public function evaluatePhp()
    {
        // Convert errors and notices into exception so we can catch them before they break the responses
        // from https://www.php.net/manual/en/class.errorexception.php
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }

            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        $return = eval($this->phpForEval());

        restore_error_handler();

        return $return;
    }
}
