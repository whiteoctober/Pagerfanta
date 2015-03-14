<?php

/**
 * This file is part of the Pagerfanta project.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Adapter;

use Elastica\Search;

class ElasticaSearchAdapter implements AdapterInterface
{
    /** @var Search */
    private $search;

    public function __construct(Search $s)
    {
        $this->search = $s;
    }

    public function getNbResults()
    {
        return $this->search->count();
    }

    public function getSlice($offset, $length)
    {
        return $this->search->search(
            '',
            array(
                'from' => $offset,
                'size' => $length
            )
        );
    }
}
