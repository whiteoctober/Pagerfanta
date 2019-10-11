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

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use \Doctrine\ORM\EntityManager;

/**
 * DoctrineORMAdapter.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class DoctrineORMAdapter implements AdapterInterface
{
    /**
     * @var \Doctrine\ORM\Tools\Pagination\Paginator
     */
    private $paginator;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param \Doctrine\ORM\EntityManager|null               $em                  Entity Manager
     * @param Boolean                                        $fetchJoinCollection Whether the query joins a collection (true by default).
     * @param Boolean|null                                   $useOutputWalkers    Whether to use output walkers pagination mode
     */
    public function __construct($query, $em = null, $fetchJoinCollection = true, $useOutputWalkers = null)
    {
        $this->query = $query;
        $this->paginator = new DoctrinePaginator($query, $fetchJoinCollection);
        $this->paginator->setUseOutputWalkers($useOutputWalkers);
        $this->em = $em;
    }

    /**
     * Returns the query
     *
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->paginator->getQuery();
    }

    /**
     * Returns whether the query joins a collection.
     *
     * @return Boolean Whether the query joins a collection.
     */
    public function getFetchJoinCollection()
    {
        return $this->paginator->getFetchJoinCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        
        if($this->em === null || empty($this->em)){
            return count($this->paginator);
        } else {
        $queryObj = $this->paginator->getQuery();

//      Pega a query gerada como string sem os par�metros
        $sql = $queryObj->getSQL();

//      Cria e usa fun��o para pegar os par�metros em ordem
        $getSqlWithParams = \Closure::bind(function($query){
            return [$query->getSql(), $query->processParameterMappings($query->_parserResult->getParameterMappings())];
        }, null, \Doctrine\ORM\Query::class);
        $qparams = $getSqlWithParams($queryObj);

//      Percorre pelos par�metros substituindo os '?' na query um por um
        foreach ($qparams[1][0] as $value){
            if(strpos($sql, '?') !== false){
                if(is_array($value)){
                    $sql = substr_replace($sql, implode(',', $value), strpos($sql, '?'), 1);
                } else {
                    $sql = substr_replace($sql, $value, strpos($sql, '?'), 1);
                }
            }
        }


//      Cria a tabela temporaria com  query tratada, faz a contagem da mesma e por fim a exclui
        $conn = $conn = $this->em->getConnection();
        $sql = 'CREATE TEMPORARY TABLE stage AS ' . $sql;
        $conn->executeQuery($sql);
        $sql = 'SELECT COUNT(1) FROM stage';
        $total = $conn->executeQuery($sql)->fetchColumn(0);
        $sql = 'DROP TABLE stage;';
        $conn->executeQuery($sql);

        return $total;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $this->paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($length);

        return $this->paginator->getIterator();
    }
}
