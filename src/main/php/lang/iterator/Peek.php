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
 * @since  5.2.0
 */
class Peek extends \IteratorIterator
{
    /**
     * consumer for values
     *
     * @type  callable
     */
    private $valueConsumer;
    /**
     * consumer for keys
     *
     * @type  callable
     */
    private $keyConsumer;

    /**
     * constructor
     *
     * @param  \Iterator  $iterator  iterator to map values of
     * @param  callable   $valueConsumer  consumer which is invoked with current value
     * @param  callable   $keyConsumer    optional  consumer which is invoked with current key
     */
    public function __construct(\Iterator $iterator, callable $valueConsumer, callable $keyConsumer = null)
    {
        parent::__construct($iterator);
        $this->valueConsumer = $valueConsumer;
        $this->keyConsumer   = $keyConsumer;
    }

    /**
     * returns the current element
     *
     * @return  mixed
     */
    public function current()
    {
        $consumeValue = $this->valueConsumer;
        $current = parent::current();
        $consumeValue($current);
        return $current;
    }

    /**
     * returns the current key
     *
     * @return  mixed
     */
    public function key()
    {
        $key = parent::key();
        if (null !== $this->keyConsumer) {
            $consumeKey = $this->keyConsumer;
            $consumeKey($key);
        }

        return $key;
    }
}
