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

/**
 * SolariumAdapter.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class SolariumAdapter implements AdapterInterface
{
    /**
     * @var \Solarium_Client
     */
    private $client;

    /**
     * @var \Solarium_Query_Select
     */
    private $query;

    /**
     * @var \Solarium_Result_Select
     */
    private $resultSet = null;

    /**
     * Constructor.
     *
     * @param \Solarium_Client $client A Solarium client
     * @param \Solarium_Query_Select $query A Solarium select query
     */
    public function __construct(\Solarium_Client $client, \Solarium_Query_Select $query)
    {
        $this->client = $client;
        $this->query = $query;
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
        $this->query
            ->setStart($offset)
            ->setRows($length);

        return $this->getResultSet();
    }

    /**
     * Solr select
     *
     * @return \Solarium_Result_Select
     */
    private function getResultSet()
    {
        if ($this->resultSet === null) {
            $this->resultSet = $this->client->select($this->query);
        }

        return $this->resultSet;
    }
}