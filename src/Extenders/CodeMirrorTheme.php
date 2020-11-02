<?php

namespace ClarkWinkelmann\Scratchpad\Extenders;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Flarum\Frontend\Document;
use Flarum\Frontend\Frontend;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Container\Container;

class CodeMirrorTheme implements ExtenderInterface
{
    public function extend(Container $container, Extension $extension = null)
    {
        $container->resolving(
            'flarum.frontend.admin',
            function (Frontend $frontend) {
                $theme = $this->getTheme();

                if (!$theme) {
                    return;
                }

                $frontend->content(function (Document $document) use ($theme) {
                    $document->head[] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.52.0/theme/' . $theme . '.css">';
                });
            }
        );

        $container['events']->listen(Serializing::class, [$this, 'attributes']);
    }

    public function attributes(Serializing $event)
    {
        // We pass the theme via forum attributes, that way when the setting was changed but the page not refreshed,
        // The theme used will continue to match the one loaded from the CDN
        if ($event->serializer instanceof ForumSerializer) {
            $event->attributes['scratchpadTheme'] = $this->getTheme();
        }
    }

    protected function getTheme()
    {
        /**
         * @var $setting SettingsRepositoryInterface
         */
        $setting = app(SettingsRepositoryInterface::class);

        $theme = $setting->get('scratchpad.theme');

        if (empty($theme) || $theme === 'auto') {
            if ($setting->get('theme_dark_mode') === '1') {
                $theme = 'darcula';
            } else {
                return null;
            }
        }

        return $theme;
    }
}
