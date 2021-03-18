<?php

namespace ClarkWinkelmann\Scratchpad;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend;
use Flarum\Http\Middleware\AuthenticateWithSession;

$extenders = [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/resources/less/admin.less')
        ->content(Content\AddThemeCss::class),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Routes('api'))
        ->get('/scratchpads', 'scratchpad.api.index', Controllers\ListScratchpadController::class)
        ->post('/scratchpads', 'scratchpad.api.store', Controllers\CreateScratchpadController::class)
        ->patch('/scratchpads/{id:[0-9]+}', 'scratchpad.api.update', Controllers\UpdateScratchpadController::class)
        ->delete('/scratchpads/{id:[0-9]+}', 'scratchpad.api.delete', Controllers\DeleteScratchpadController::class)
        ->post('/scratchpads/{id:[0-9]+}/compile', 'scratchpad.api.compile', Controllers\CompileScratchpadController::class),

    (new Extend\Middleware('forum'))
        ->insertAfter(AuthenticateWithSession::class, Middlewares\AuthenticateWithLiveToken::class),
    (new Extend\Middleware('admin'))
        ->insertAfter(AuthenticateWithSession::class, Middlewares\AuthenticateWithLiveToken::class),

    (new Extend\ErrorHandling())
        ->handler(ErrorHandling\ValidationExceptionWithMeta::class, ErrorHandling\ValidationExceptionWithMetaHandler::class),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(ForumAttributes::class),

    (new Extend\ServiceProvider())
        ->register(Providers\RegisterAssets::class)
        ->register(Providers\TestRoutes::class),
];

LiveCodeHelper::boot();

/**
 * @var $repository ScratchpadRepository
 */
$repository = resolve(ScratchpadRepository::class);

try {
    foreach ($repository->allEnabled() as $scratchpad) {
        if ($scratchpad->id === LiveCodeHelper::$ignoreScratchpadId) {
            continue;
        }

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

if (LiveCodeHelper::$php) {
    $extenders = array_merge($extenders, PHPEvaluator::evaluate(LiveCodeHelper::$php));
}

return $extenders;
