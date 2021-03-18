<?php

namespace ClarkWinkelmann\Scratchpad\Providers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\LiveCodeHelper;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Frontend\Assets;
use Flarum\Frontend\Compiler\Source\SourceCollector;

class RegisterAssets extends AbstractServiceProvider
{
    public function register()
    {
        /**
         * @var $repository ScratchpadRepository
         */
        $repository = $this->container->make(ScratchpadRepository::class);

        foreach ($repository->allEnabled() as $scratchpad) {
            $this->registerScratchpad($scratchpad, 'admin');
            $this->registerScratchpad($scratchpad, 'forum');
        }

        if (LiveCodeHelper::$forumLess) {
            $this->container->resolving('flarum.assets.forum', function (Assets $assets) {
                $assets->css(function (SourceCollector $sources) {
                    $sources->addString(function () {
                        return LiveCodeHelper::$forumLess;
                    });
                });
            });
        }

        if (LiveCodeHelper::$adminLess) {
            $this->container->resolving('flarum.assets.admin', function (Assets $assets) {
                $assets->css(function (SourceCollector $sources) {
                    $sources->addString(function () {
                        return LiveCodeHelper::$adminLess;
                    });
                });
            });
        }
    }

    protected function registerScratchpad(Scratchpad $scratchpad, string $frontend)
    {
        $js = $scratchpad->{$frontend . '_js_compiled'};
        $less = $scratchpad->id === LiveCodeHelper::$ignoreScratchpadId ? '' : $scratchpad->{$frontend . '_less'};

        if (empty($js) && empty($less)) {
            return;
        }

        $moduleName = 'scratchpad' . $scratchpad->id;

        $this->container->resolving('flarum.assets.' . $frontend, function (Assets $assets) use ($js, $less, $moduleName) {
            if ($js) {
                $assets->js(function (SourceCollector $sources) use ($js, $moduleName) {
                    $sources->addString(function () use ($js, $moduleName) {
                        return "var module={}\n$js\nflarum.extensions['$moduleName']=module.exports";
                    });
                });
            }

            if ($less) {
                $assets->css(function (SourceCollector $sources) use ($less) {
                    $sources->addString(function () use ($less) {
                        return $less;
                    });
                });
            }
        });
    }
}
