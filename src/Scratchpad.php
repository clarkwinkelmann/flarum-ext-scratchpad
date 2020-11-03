<?php

namespace ClarkWinkelmann\Scratchpad;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;

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

    public function evaluatePhp()
    {
        return PHPEvaluator::evaluate($this->php);
    }
}
