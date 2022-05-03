<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/JsonUpdateRequestBuilder.php';
require_once __DIR__ . '/JsonUpdateQuery.php';

$config = [
        'endpoint' => [
                'catalog' => [
                        'host' => '127.0.0.1',
                        'port' => '8983',
                        'path' => '/',
                        'core' => 'catalog',
                ],
        ],
];

$adapter = new \Solarium\Core\Client\Adapter\Curl();
$eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
$client = new \Solarium\Client($adapter, $eventDispatcher, $config);
// Crucial, allows Solarium to make JSON update requests to Solr.
$client->registerQueryType(\Solarium\Client::QUERY_UPDATE, JsonUpdateQuery::class);

$data = json_decode('{
    "id": 1,
    "docType": "Product",
    "manufacturer": {
        "docType": "Manufacturer",
        "id": 2,
        "name": "Apple"
    }
}', true);
$doc = new \Solarium\QueryType\Update\Query\Document($data);
var_dump($doc->getFields());

$update = $client->createUpdate();
$update->addDocument($doc)->addCommit(true);
$client->update($update);

// Select data from index to see that the single nested child is properly indexed.
$select = $client->createSelect();
$select->createFilterQuery('productsOnly')->setQuery('docType:Product');
$select->setFields('* [child limit=-1]');
$result = $client->select($select);
var_dump($result->getDocuments());
