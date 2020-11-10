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
 * Adapter that allows limit maximum number of results. It can be useful for preventing
 * slow down queries with large offsets or get rid of errors like "result window too large".
 *
 * @author Konstantin Myakshin <molodchick@gmail.com>
 */
class LimitedAdapter implements AdapterInterface
{
    private $decorated;

    private $maxNbResults;

    public function __construct(AdapterInterface $decorated, int $maxNbResults)
    {
        $this->decorated = $decorated;
        $this->maxNbResults = $maxNbResults;
    }

    public function getNbResults()
    {
        return min($this->decorated->getNbResults(), $this->maxNbResults);
    }

    public function getSlice($offset, $length)
    {
        return $this->decorated->getSlice($offset, $length);
    }
}
