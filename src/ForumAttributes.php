<?php

namespace ClarkWinkelmann\Scratchpad;

use Flarum\Foundation\Config;

class ForumAttributes
{
    protected $theme;
    protected $config;

    public function __construct(ThemeManager $theme, Config $config)
    {
        $this->theme = $theme;
        $this->config = $config;
    }

    public function __invoke(): array
    {
        $attributes = [
            'scratchpadTheme' => $this->theme->getTheme(),
        ];

        if (count(PHPLoadErrors::$errors) && $this->config->inDebugMode()) {
            $attributes['scratchpadPhpErrors'] = PHPLoadErrors::$errors;
        }

        return $attributes;
    }
}
