<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\Adapter\DoctrineORM;

use Doctrine\ORM\Query\TreeWalkerAdapter,
    Doctrine\ORM\Query\AST\SelectStatement,
    Doctrine\ORM\Query\AST\SelectExpression,
    Doctrine\ORM\Query\AST\PathExpression,
    Doctrine\ORM\Query\AST\AggregateExpression,
    Doctrine\ORM\Version;

class CountWalker extends TreeWalkerAdapter
{

    /**
     * Walks down a SelectStatement AST node, modifying it to retrieve a COUNT
     *
     * @param SelectStatement $AST
     * @return void
     */
    public function walkSelectStatement(SelectStatement $AST)
    {
        $parent = null;
        $parentName = null;



        foreach ($this->_getQueryComponents() AS $dqlAlias => $qComp) {

            // skip mixed data in query
            if (isset($qComp['resultVariable'])) {
                continue;
            }

            if ($qComp['parent'] === null && $qComp['nestingLevel'] == 0) {
                $parent = $qComp;
                $parentName = $dqlAlias;
                break;
            }
        }


        $pathExpression = new PathExpression(
                        PathExpression::TYPE_STATE_FIELD | PathExpression::TYPE_SINGLE_VALUED_ASSOCIATION, $parentName,
                        $parent['metadata']->getSingleIdentifierFieldName()
        );
        $pathExpression->type = PathExpression::TYPE_STATE_FIELD;

        $aggregateExpression = new AggregateExpression('count', $pathExpression, true);
        if (Version::compare('2.2.0-DEV') < 0) {
            $selectExpression = new SelectExpression($aggregateExpression, null);
        } else {
            $selectExpression = new SelectExpression($aggregateExpression, false, null);
        }
        $AST->selectClause->selectExpressions = array(
            $selectExpression
        );

        // ORDER BY is not needed, only increases query execution through unnecessary sorting.
        $AST->orderByClause = null;
    }

}
