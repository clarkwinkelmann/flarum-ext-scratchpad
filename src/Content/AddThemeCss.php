<?php

namespace ClarkWinkelmann\Scratchpad\Content;

use ClarkWinkelmann\Scratchpad\ThemeManager;
use Flarum\Frontend\Document;

class AddThemeCss
{
    protected $theme;

    public function __construct(ThemeManager $theme)
    {
        $this->theme = $theme;
    }

    public function __invoke(Document $document)
    {
        $theme = $this->theme->getTheme();

        if (!$theme) {
            return;
        }

        $document->head[] = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.52.0/theme/' . $theme . '.css">';
    }
}
