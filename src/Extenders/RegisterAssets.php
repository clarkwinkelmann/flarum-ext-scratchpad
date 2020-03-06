<?php

namespace ClarkWinkelmann\Scratchpad\Extenders;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Frontend\Assets;
use Flarum\Frontend\Compiler\Source\SourceCollector;
use Illuminate\Contracts\Container\Container;

class RegisterAssets implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        /**
         * @var $repository ScratchpadRepository
         */
        $repository = app(ScratchpadRepository::class);

        foreach ($repository->allEnabled() as $scratchpad) {
            $this->registerScratchpad($container, $scratchpad, 'admin');
            $this->registerScratchpad($container, $scratchpad, 'forum');
        }
    }

    protected function registerScratchpad(Container $container, Scratchpad $scratchpad, string $frontend)
    {
        $js = $scratchpad->{$frontend . '_js_compiled'};
        $less = $scratchpad->{$frontend . '_less'};

        if (empty($js) && empty($less)) {
            return;
        }

        $moduleName = 'scratchpad' . $scratchpad->id;

        $container->resolving('flarum.assets.' . $frontend, function (Assets $assets) use ($js, $less, $moduleName) {
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
