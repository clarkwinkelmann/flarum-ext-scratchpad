<?php

namespace ClarkWinkelmann\Scratchpad;

use ClarkWinkelmann\Scratchpad\ErrorHandling\InvalidLiveTokenException;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;

class LiveCodeHelper
{
    public static $actorId;
    public static $ignoreScratchpadId;
    public static $adminLess;
    public static $forumLess;
    public static $php;

    public static function boot()
    {
        if (
            !Arr::has($_REQUEST, 'scratchpadLiveAdminLess') &&
            !Arr::has($_REQUEST, 'scratchpadLiveForumLess') &&
            !Arr::has($_REQUEST, 'scratchpadLivePhp')
        ) {
            return;
        }

        /**
         * @var $settings SettingsRepositoryInterface
         */
        $settings = resolve(SettingsRepositoryInterface::class);

        $token = $settings->get('scratchpad.liveCodeToken');

        if (!$token || Arr::get($_REQUEST, 'scratchpadLiveToken') !== $token) {
            throw new InvalidLiveTokenException();
        }

        self::$actorId = Arr::get($_REQUEST, 'scratchpadLiveActorId');
        self::$ignoreScratchpadId = Arr::get($_REQUEST, 'scratchpadLiveIgnoreId');
        self::$adminLess = Arr::get($_REQUEST, 'scratchpadLiveAdminLess', '');
        self::$forumLess = Arr::get($_REQUEST, 'scratchpadLiveForumLess', '');
        self::$php = Arr::get($_REQUEST, 'scratchpadLivePhp', '');
    }
}
