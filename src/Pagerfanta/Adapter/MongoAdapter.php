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
 * MongoAdapter.
 *
 * @author Sergey Ponomaryov <serg.ponomaryov@gmail.com>
 *
 * @api
 */
class MongoAdapter implements AdapterInterface
{
    private $cursor;

    /**
     * Constructor.
     *
     * @param \MongoCursor $cursor The cursor.
     *
     * @api
     */
    public function __construct(\MongoCursor $cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * Returns the cursor.
     *
     * @return \MongoCursor The cursor.
     *
     * @api
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->cursor->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->cursor->limit($length)->skip($offset);
    }
}
