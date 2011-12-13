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
 *
 * @api
 */
class SolariumAdapter implements AdapterInterface
{
    private $client;
    private $query;

    /**
     * Constructor.
     *
     * @param Solarium_Query_Select  $query           A Solarium select query.
     *
     * @api
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

    private function getResultSet()
    {
        return $this->client->select($this->query);
    }
}
