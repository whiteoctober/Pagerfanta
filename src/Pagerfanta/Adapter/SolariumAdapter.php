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
 */
class SolariumAdapter implements AdapterInterface
{
    private $client;
    private $query;
    private $resultSet;
    private $endPoint;
    private $resultSetStart;
    private $resultSetRows;

    /**
     * Constructor.
     *
     * @param Solarium_Client|Client       $client A Solarium client.
     * @param Solarium_Query_Select|Query  $query  A Solarium select query.
     */
    public function __construct($client, $query)
    {
        $this->checkClient($client);
        $this->checkQuery($query);

        $this->client = $client;
        $this->query = $query;
    }

    private function checkClient($client)
    {
        if ($this->isClientInvalid($client)) {
            throw new InvalidArgumentException($this->getClientInvalidMessage($client));
        }
    }

    private function isClientInvalid($client)
    {
        return !($client instanceof Client) &&
               !($client instanceof Solarium_Client);
    }

    private function getClientInvalidMessage($client)
    {
        return sprintf('The client object should be a Solarium_Client or Solarium\Core\Client\Client instance, %s given',
            get_class($client)
        );
    }

    private function checkQuery($query)
    {
        if ($this->isQueryInvalid($query)) {
            throw new InvalidArgumentException($this->getQueryInvalidMessage($query));
        }
    }

    private function isQueryInvalid($query)
    {
        return !($query instanceof Query) &&
               !($query instanceof Solarium_Query_Select);
    }

    private function getQueryInvalidMessage($query)
    {
        return sprintf('The query object should be a Solarium_Query_Select or Solarium\QueryType\Select\Query\Query instance, %s given',
            get_class($query)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->getResultSet()->getNumFound();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->getResultSet($offset, $length);
    }

    /**
     * @return Solarium_Result_Select|Solarium\QueryType\Select\Result\Result
     **/
    public function getResultSet($start = null, $rows = null)
    {
        if ($this->resultSet === null || $this->resultSetStartOrRowsChange($start, $rows)) {
            if (null !== $start || null !== $rows) {
                $this->query
                    ->setStart($start)
                    ->setRows($rows)
                ;
            }

            $this->resultSet = $this->createResultSet();
            $this->resultSetStart = $start;
            $this->resultSetRows = $rows;
        }

        return $this->resultSet;
    }

    private function resultSetStartOrRowsChange($start, $rows)
    {
        return
            null !== $start && null !== $rows
            && ($start !== $this->resultSetStart || $rows !== $this->resultSetRows)
        ;
    }

    private function createResultSet()
    {
        return $this->client->select($this->query, $this->endPoint);
    }

    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;

        return $this;
    }
}
