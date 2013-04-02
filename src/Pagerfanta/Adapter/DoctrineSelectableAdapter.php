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

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;

/**
 * DoctrineSelectableAdapter.
 *
 * @author Boris Guéry <guery.b@gmail.com>
 *
 * @api
 */
class DoctrineSelectableAdapter implements AdapterInterface
{
    /**
     * @var Selectable
     */
    private $selectable;

    /**
     * @var Criteria
     */
    private $criteria;

    /**
     * Constructor.
     *
     * @param Selectable $selectable An implementation of the Selectable interface.
     * @param Criteria   $criteria   A Doctrine criteria.
     *
     * @api
     */
    public function __construct(Selectable $selectable, Criteria $criteria = null)
    {
        $this->selectable = $selectable;
        $this->criteria   = ($criteria) ?: new Criteria();
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $criteria = clone $this->criteria;
        $criteria->setFirstResult(null);
        $criteria->setMaxResults(null);

        return $this->selectable->matching($criteria)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $criteria = clone $this->criteria;
        $criteria->setFirstResult($offset);
        $criteria->setMaxResults($length);

        return $this->selectable->matching($this->criteria);
    }
}
