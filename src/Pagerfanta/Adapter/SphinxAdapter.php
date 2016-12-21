<?php
/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Maikel Doezé <maikel.doeze@marqin.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Pagerfanta\Adapter;

use SphinxClient;

class SphinxAdapter implements AdapterInterface
{
	private $client;
	private $query;
    private $index;
    private $comment;
    private $results;
	
	/**
	 * Constructor.
	 *
	 * @param SphinxClient $client A Sphinx client.
	 * @param $query A Sphinx query.
     * @param $index A Sphinx index.
     * @param $comment A Sphinx comment.
	 */
	public function __construct(SphinxClient $client, $query, $index = "*", $comment = "")
	{
		$this->client = $client;
        $this->query = $query;
        $this->index = $index;
        $this->comment = $comment;
	}
	
	/**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        if (!$this->results) {
           return $this->client->query($this->query, $this->index, $this->comment)['total_found'];
        }

        return $this->results['total_found'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $limit)
    {
        // Set limit
        $this->client->setLimits($offset,$limit);
        
        return $this->results = $this->client
            ->query($this->query, $this->index, $this->comment);
    }
}
