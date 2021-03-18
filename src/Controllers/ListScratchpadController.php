<?php

namespace ClarkWinkelmann\Scratchpad\Controllers;

use ClarkWinkelmann\Scratchpad\ScratchpadRepository;
use ClarkWinkelmann\Scratchpad\Serializers\ScratchpadSerializer;
use Flarum\Api\Controller\AbstractListController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListScratchpadController extends AbstractListController
{
    public $serializer = ScratchpadSerializer::class;

    protected $repository;

    public function __construct(ScratchpadRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $request->getAttribute('actor')->assertAdmin();

        return $this->repository->all();
    }
}
