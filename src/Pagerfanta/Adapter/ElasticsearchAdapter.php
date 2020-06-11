<?php

namespace Pagerfanta\Adapter;

use Elasticsearch\Client;

/**
 * ElasticsearchAdapter.
 *
 * Used with the official Elasticsearch PHP library.
 * 
 * @author Danny Sipos <danny@webomelette.com>
 */
class ElasticsearchAdapter implements AdapterInterface
{

  /**
   * The Elasticsearch query parameters.
   *
   * @var array
   */
  private $params;

  /**
   * The Elasticsearch results
   *
   * @var array
   */
  private $results;

  /**
   * The Elasticsearch client.
   *
   * @var Client
   */
  private $client;

  /**
   * Constructor.
   *
   * @param array $params The array of parameters to use for performing the search.
   * @param Client $client The Elasticsearch client.
   */
  public function __construct(array $params, Client $client)
  {
    $this->params = $params;
    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public function getNbResults()
  {
    if (!$this->results) {
      $this->runQuery();
    }
    return $this->results['hits']['total'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSlice($offset, $length)
  {
    $params = $this->params;
    $params['from'] = $offset;
    $params['size'] = $length;
    $this->runQuery($params);
    return $this->results;
  }

  /**
   * Runs the query and stores the results.
   *
   * @param array $params
   */
  private function runQuery($params = array())
  {
    if (!$params) {
      $params = $this->params;
    }
    $this->results = $this->client->search($params);
  }
}
