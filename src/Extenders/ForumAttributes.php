<?php

namespace ClarkWinkelmann\Scratchpad\Extenders;

use ClarkWinkelmann\Scratchpad\PHPLoadErrors;
use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

class ForumAttributes implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        $container['events']->listen(Serializing::class, [$this, 'attributes']);
    }

    public function attributes(Serializing $event)
    {
        if ($event->serializer instanceof ForumSerializer && count(PHPLoadErrors::$errors) && app()->inDebugMode()) {
            $event->attributes['scratchpadPhpErrors'] = PHPLoadErrors::$errors;
        }
    }
}
