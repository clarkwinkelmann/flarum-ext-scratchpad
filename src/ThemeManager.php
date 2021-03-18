<?php

namespace ClarkWinkelmann\Scratchpad;

use Flarum\Settings\SettingsRepositoryInterface;

class ThemeManager
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function getTheme(): ?string
    {
        $theme = $this->settings->get('scratchpad.theme');

        if (empty($theme) || $theme === 'auto') {
            if ($this->settings->get('theme_dark_mode') === '1') {
                $theme = 'darcula';
            } else {
                return null;
            }
        }

        return $theme;
    }
}
