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
 * Iterator which allows consumption of an element before iteration continues.
 *
 * @since  4.1.0
 */
class Peek extends \IteratorIterator
{
    /**
     * actual mapping functionality
     *
     * @type  callable
     */
    private $consumer;

    /**
     * constructor
     *
     * @param  \Iterator  $iterator  iterator to map values of
     * @param  callable   $consumer  consumer which is invoked with current element
     */
    public function __construct(\Iterator $iterator, callable $consumer)
    {
        parent::__construct($iterator);
        $this->consumer = $consumer;
    }

    /**
     * returns the current element
     *
     * @return  string
     */
    public function current()
    {
        $consume = $this->consumer;
        $current = parent::current();
        $consume($current);
        return $current;
    }
}
