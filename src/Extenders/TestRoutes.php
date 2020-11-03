<?php

namespace ClarkWinkelmann\Scratchpad\Extenders;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Http\RouteCollection;
use Flarum\Http\RouteHandlerFactory;
use Illuminate\Contracts\Container\Container;

class TestRoutes implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        $container->resolving(
            'flarum.forum.routes',
            function (RouteCollection $collection, Container $container) {
                /** @var RouteHandlerFactory $factory */
                $factory = $container->make(RouteHandlerFactory::class);

                $collection->post('/scratchpad/test', 'scratchpad.forum.test', $factory->toFrontend('forum'));
            }
        );

        $container->resolving(
            'flarum.admin.routes',
            function (RouteCollection $collection, Container $container) {
                /** @var RouteHandlerFactory $factory */
                $factory = $container->make(RouteHandlerFactory::class);

                $collection->post('/scratchpad/test', 'scratchpad.admin.test', $factory->toFrontend('admin'));
            }
        );
    }
}
