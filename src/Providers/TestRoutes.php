<?php

namespace ClarkWinkelmann\Scratchpad\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Http\RouteCollection;
use Flarum\Http\RouteHandlerFactory;
use Illuminate\Contracts\Container\Container;

class TestRoutes extends AbstractServiceProvider
{
    public function register()
    {
        // We need to use a service provider instead of Extend\Frontend::route because we need a POST route
        $this->container->resolving(
            'flarum.forum.routes',
            function (RouteCollection $collection, Container $container) {
                /** @var RouteHandlerFactory $factory */
                $factory = $container->make(RouteHandlerFactory::class);

                $collection->post('/scratchpad/test', 'scratchpad.forum.test', $factory->toFrontend('forum'));
            }
        );

        $this->container->resolving(
            'flarum.admin.routes',
            function (RouteCollection $collection, Container $container) {
                /** @var RouteHandlerFactory $factory */
                $factory = $container->make(RouteHandlerFactory::class);

                $collection->post('/scratchpad/test', 'scratchpad.admin.test', $factory->toFrontend('admin'));
            }
        );
    }
}
