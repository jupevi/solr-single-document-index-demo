<?php

declare(strict_types=1);

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Document;

class JsonUpdateRequestBuilder extends AbstractRequestBuilder {
    public function build(AbstractQuery $query): Request {
        if (!($query instanceof JsonUpdateQuery)) {
            throw new LogicException(
                __CLASS__ . ' can only build a request from queries of type ' . JsonUpdateQuery::class);
        }
        $query->setOptions(['handler' => 'update/json']);
        $request = parent::build($query);
        $request->setMethod(Request::METHOD_POST);
        $request->setRawData($this->buildRawData($query));
        return $request;
    }


    private function buildRawData(JsonUpdateQuery $query): string {
        $out = [];
        foreach ($query->getCommands() as $command) {
            if ($command instanceof Add) {
                $out['add'] = array_merge($out['add'] ?? [], $this->buildAddArray($command));
            } else if ($command instanceof Delete) {
                $out['delete'] = array_merge($out['delete'] ?? [], $this->buildDeleteArray($command));
            } else if ($command instanceof Commit) {
                $out['commit'] = array_merge($out['commit'] ?? [], $this->buildCommitArray($command));
            } else {
                throw new LogicException("Unsupported command type {$command->getType()} given to " . __CLASS__);
            }
        }
        // Solr is picky with some values; notably `commit` must be an object, not an array. Coerce empty array to object.
        if (isset($out['commit'])) {
            $out['commit'] = (object) $out['commit'];
        }
        return json_encode($out);
    }


    private function buildAddArray(Add $command): array {
        return array_map(fn(Document $doc) => $doc->getFields(), $command->getDocuments());
    }


    private function buildDeleteArray(Delete $command): array {
        return $command->getIds();
    }


    private function buildCommitArray(Commit $command): array {
        return array_filter(
            [
                'softCommit' => $command->getSoftCommit(),
                'waitSearcher' => $command->getWaitSearcher(),
                'expungeDeletes' => $command->getExpungeDeletes(),
            ],
            static fn($v) => !is_null($v),
        );
    }
}

