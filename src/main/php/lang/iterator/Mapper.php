<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
/**
 * Maps values from an iterator.
 *
 * @since  4.1.0
 */
class Mapper extends \IteratorIterator
{
    /**
     * actual mapping functionality
     *
     * @type  callable
     */
    private $mapper;

    /**
     * constructor
     *
     * @param  \Iterator  $iterator  iterator to map values of
     * @param  callable   $mapper    actual mapping functionality
     */
    public function __construct(\Iterator $iterator, callable $mapper)
    {
        parent::__construct($iterator);
        $this->mapper = $mapper;
    }

    /**
     * returns the current element
     *
     * @return  string
     */
    public function current()
    {
        $map = $this->mapper;
        return $map(parent::current());
    }
}
