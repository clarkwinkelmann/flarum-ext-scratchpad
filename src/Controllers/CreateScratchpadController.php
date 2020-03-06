<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\Serializers\ScratchpadSerializer;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\User\AssertPermissionTrait;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateScratchpadController extends AbstractCreateController
{
    use AssertPermissionTrait;

    public $serializer = ScratchpadSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $this->assertAdmin($request->getAttribute('actor'));

        $scratchpad = new Scratchpad();

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

        /**
         * @var $repository ScratchpadRepository
         */
        $repository = app(ScratchpadRepository::class);

        $repository->validateAndFill($scratchpad, $attributes);

        $scratchpad->save();

        return $scratchpad;
    }
}
