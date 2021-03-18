<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\Serializers\ScratchpadSerializer;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateScratchpadController extends AbstractShowController
{
    public $serializer = ScratchpadSerializer::class;

    protected $repository;

    public function __construct(ScratchpadRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        /**
         * @var $actor User
         */
        $actor = $request->getAttribute('actor');

        $actor->assertAdmin();

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

            $this->repository->validateAndFill($scratchpad, $attributes, $actor);
        }

        $scratchpad->save();

        return $scratchpad;
    }
}
