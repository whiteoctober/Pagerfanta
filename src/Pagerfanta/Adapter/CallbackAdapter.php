<?php

namespace Pagerfanta\Adapter;

use Pagerfanta\Exception\InvalidArgumentException;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class CallbackAdapter implements AdapterInterface
{
    /**
     * @var callable
     */
    private $nbResultsCallback;

    /**
     * @var callable
     */
    private $sliceCallback;

    /**
     * @param callable $nbResultsCallback
     * @param callable $sliceCallback
     */
    public function __construct($nbResultsCallback, $sliceCallback)
    {
        if (!is_callable($nbResultsCallback)) {
            throw new InvalidArgumentException(sprintf('$nbResultsCallback should be a callable'));
        }
        if (!is_callable($sliceCallback)) {
            throw new InvalidArgumentException(sprintf('$sliceCallback should be a callable'));
        }

        $this->nbResultsCallback = $nbResultsCallback;
        $this->sliceCallback = $sliceCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return call_user_func($this->nbResultsCallback);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return call_user_func_array($this->sliceCallback, array($offset, $length));
    }
}
