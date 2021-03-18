<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\Scratchpad;
use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\Serializers\ScratchpadSerializer;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateScratchpadController extends AbstractCreateController
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

        $scratchpad = new Scratchpad();

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $this->repository->validateAndFill($scratchpad, $attributes, $actor);

        $scratchpad->save();

        return $scratchpad;
    }
}
