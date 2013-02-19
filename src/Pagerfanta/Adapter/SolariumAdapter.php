<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Adapter;

use Pagerfanta\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;
use Solarium\Core\Client\Client;
use Solarium_Query_Select;
use Solarium_Client;

/**
 * SolariumAdapter.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 *
 * @api
 */
class SolariumAdapter implements AdapterInterface
{
    private $client;
    private $query;
    private $cachedResultSet;

    /**
     * Constructor.
     *
     * @param Solarium_Client|Client       $client A Solarium client.
     * @param Solarium_Query_Select|Query  $query  A Solarium select query.
     *
     * @api
     */
    public function __construct($client, $query)
    {
        if ((!$query instanceof Query) && (!$query instanceof Solarium_Query_Select)) {
            throw new InvalidArgumentException('The query object should be a Solarium_Query_Select or Solarium\QueryType\Select\Query\Query instance, '.get_class($query).' given');
        }
        if ((!$client instanceof Client) && (!$client instanceof Solarium_Client)) {
            throw new InvalidArgumentException('The client object should be a Solarium_Client or Solarium\Core\Client\Client instance, '.get_class($client).' given');
        }
        $this->client = $client;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->getCachedResultSet()->getNumFound();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $this->query
            ->setStart($offset)
            ->setRows($length);

        $this->cachedResultSet = $this->getResultSet();

        return $this->cachedResultSet;
    }

    private function getResultSet()
    {
        return $this->client->select($this->query);
    }

    private function getCachedResultSet()
    {
        return $this->cachedResultSet ?: $this->getResultSet();
    }
}
