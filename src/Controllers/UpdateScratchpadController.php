<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\Serializers\ScratchpadSerializer;
use Flarum\Api\Controller\AbstractShowController;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateScratchpadController extends AbstractShowController
{
    public $serializer = ScratchpadSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $request->getAttribute('actor')->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        /**
         * @var $scratchpad Scratchpad
         */
        $scratchpad = Scratchpad::query()->findOrFail($id);

        $enabled = Arr::get($request->getParsedBody(), 'data.attributes.enabled');

        if (!is_null($enabled)) {
            $scratchpad->enabled = $enabled;
        } else {
            $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

            /**
             * @var $repository ScratchpadRepository
             */
            $repository = app(ScratchpadRepository::class);

            $repository->validateAndFill($scratchpad, $attributes);
        }

        if ($scratchpad->isDirty()) {
            $scratchpad->save();
        }

        return $scratchpad;
    }
}
