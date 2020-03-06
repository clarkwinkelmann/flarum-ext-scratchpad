<?php

namespace ClarkWinkelmann\Scratchpad;

use Flarum\Extend;

$extenders = [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/resources/less/admin.less'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Routes('api'))
        ->get('/scratchpads', 'scratchpad.api.index', Controllers\ListScratchpadController::class)
        ->post('/scratchpads', 'scratchpad.api.store', Controllers\CreateScratchpadController::class)
        ->patch('/scratchpads/{id:[0-9]+}', 'scratchpad.api.update', Controllers\UpdateScratchpadController::class)
        ->delete('/scratchpads/{id:[0-9]+}', 'scratchpad.api.delete', Controllers\DeleteScratchpadController::class)
        ->post('/scratchpads/{id:[0-9]+}/compile', 'scratchpad.api.compile', Controllers\CompileScratchpadController::class),

    new Extenders\CodeMirrorTheme(),
    new Extenders\ForumAttributes(),
    new Extenders\RegisterAssets(),
];

/**
 * @var $repository ScratchpadRepository
 */
$repository = app(ScratchpadRepository::class);

try {
    foreach ($repository->allEnabled() as $scratchpad) {
        try {
            $return = $scratchpad->evaluatePhp();

            if (is_array($return)) {
                $extenders = array_merge($extenders, $return);
            }
        } catch (\Throwable $exception) {
            PHPLoadErrors::record($scratchpad, $exception);
        }
    }
} catch (\Exception $exception) {
    // A table not found exception might be thrown if migrations for this extensions have not run yet
    // We will silence such an error
    // We catch \Exception because it could be either a PDOException or QueryException
    if (!str_contains($exception->getMessage(), 'scratchpads')) {
        throw $exception;
    }
}

return $extenders;
