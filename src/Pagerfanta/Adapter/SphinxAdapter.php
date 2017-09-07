<?php
/*
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
    private $maxMatches = 0;
    private $cutoff = 0;
	
	/**
	 * Constructor.
	 *
     * @param SphinxClient $client A Sphinx client.
     * @param string $query A Sphinx query.
     * @param string $index A Sphinx index.
     * @param string $comment A Sphinx comment.
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
           return $this->client->query($this->query, $this->index, $this->comment)['total'];
        }

        return $this->results['total'];
    }

    /*
     * setMaxMatches
     *
     * @param int $maxMatches Controls how much matches searchd will keep in RAM while searching.
     *
     * @return SphinxAdapter
     */
    public function setMaxMatches($maxMatches)
    {
        $this->maxMatches = $maxMatches;

        return $this;
    }

    /*
     * getMaxMatches
     *
     * @param void
     *
     * @return int
     */
    public function getMaxMatches()
    {
        return $this->maxMatches;
    }

    /*
     * setCutoff
     *
     * @param $cutoff Used for advanced performance control. It tells searchd to forcibly stop search query once cutoff matches have been found and processed.
     *
     * @return SphinxAdapter
     */
    public function setCutoff($cutoff)
    {
        $this->cutoff = $cutoff;

        return $this;
    }

    /*
     * getCutoff
     *
     * @param void
     *
     * @return int
     */
    public function getCutoff()
    {
        return $this->cutoff;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $limit)
    {
        // Set limit
        $this->client->setLimits($offset, $limit, $this->maxMatches, $this->cutoff);
        
        return $this->results = $this->client
            ->query($this->query, $this->index, $this->comment);
    }
}
