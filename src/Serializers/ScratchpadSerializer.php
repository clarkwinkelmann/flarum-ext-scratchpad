<?php

namespace ClarkWinkelmann\Scratchpad\Serializers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use Flarum\Api\Serializer\AbstractSerializer;

class ScratchpadSerializer extends AbstractSerializer
{
    protected $type = 'scratchpads';

    /**
     * @param Scratchpad $scratchpad
     * @return array
     */
    protected function getDefaultAttributes($scratchpad)
    {
        return $scratchpad->toArray();
    }
}
