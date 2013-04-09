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

use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Exception\InvalidArgumentException;

/**
 * @author Michael Williams <michael@whizdevelopment.com>
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DoctrineDbalSingleTableAdapter extends DoctrineDbalAdapter
{
    /**
     * Constructor.
     *
     * @param QueryBuilder $query      A DBAL query builder.
     * @param string       $countField Primary key for the table in query. Used in count expression. Must include table alias
     */
    public function __construct(QueryBuilder $query, $countField)
    {
        $countQueryModifier = $this->createCountQueryModifier($countField);

        parent::__construct($query, $countQueryModifier);
    }

    private function createCountQueryModifier($countField)
    {
        $select = $this->createSelectForCountField($countField);

        return function ($query) use ($select) {
            $query->select($select)
                  ->setMaxResults(1);
        };
    }

    private function createSelectForCountField($countField)
    {
        if ($this->countFieldHasNoAlias($countField)) {
            throw new InvalidArgumentException('The $countField must contain a table alias in the string.');
        }

        return sprintf('COUNT(DISTINCT %s) AS total_results', $countField);
    }

    private function countFieldHasNoAlias($countField)
    {
        return strpos($countField, '.') === false;
    }
}