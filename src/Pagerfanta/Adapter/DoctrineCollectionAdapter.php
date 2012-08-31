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

use Doctrine\Common\Collections\Collection;

/**
 * DoctrineCollectionAdapter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 *
 * @api
 */
class DoctrineCollectionAdapter extends BaseAdapter implements AdapterInterface
{
    private $collection;

    /**
     * Constructor.
     *
     * @param Collection $collection A Doctrine collection.
     *
     * @api
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Returns the collection.
     *
     * @return Collection The collection.
     *
     * @api
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        if ($this->getMaxResults() == 0) {
            return $this->collection->count();
        }

        return $this->collection->count() > $this->getMaxResults() ? $this->getMaxResults() : $this->collection->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->collection->slice($offset, $length);
    }
}
