<?php

declare(strict_types=1);

use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;

class JsonUpdateQuery extends Query {
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'update',
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'omitheader' => false,
        'json' => true,
    ];


    public function getRequestBuilder(): RequestBuilderInterface {
        if (!$this->getOption('json')) {
            return parent::getRequestBuilder();
        }
        return new JsonUpdateRequestBuilder();
    }
}

