<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use Flarum\Api\Controller\AbstractDeleteController;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteScratchpadController extends AbstractDeleteController
{
    protected function delete(ServerRequestInterface $request)
    {
        $request->getAttribute('actor')->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        /**
         * @var $scratchpad Scratchpad
         */
        $scratchpad = Scratchpad::query()->findOrFail($id);

        $scratchpad->delete();
    }
}
