<?php

namespace ClarkWinkelmann\Scratchpad\Middlewares;

use ClarkWinkelmann\Scratchpad\LiveCodeHelper;
use Flarum\User\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticateWithLiveToken implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // We need a way to authenticate the user because otherwise it's impossible to test-load the admin frontend
        if (LiveCodeHelper::$actorId) {
            $actor = User::find(LiveCodeHelper::$actorId);

            if ($actor) {
                $request = $request->withAttribute('actor', $actor);
                $request = $request->withAttribute('bypassCsrfToken', true);
                // Unlike AuthenticateWithHeader, we don't remove the session attribute
                // Doing so would create an issue in Content\CorePayload which expects the session to exist
            }
        }

        return $handler->handle($request);
    }
}
