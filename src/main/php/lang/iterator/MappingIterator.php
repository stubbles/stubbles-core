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
use stubbles\lang;
/**
 * Maps values and optionally keys from an underlying iterator.
 *
 * @since  5.0.0
 */
class MappingIterator extends \IteratorIterator
{
    /**
     * callable which maps the values‚
     *
     * @type  callable
     */
    private $valueMapper;
    /**
     * callable which maps the keys
     *
     * @type  callable
     */
    private $keyMapper;

    /**
     * constructor
     *
     * @param  \Traversable  $iterator     iterator to map values of
     * @param  callable      $valueMapper  callable which maps the values
     * @param  callable      ‚$keyMapper    callable which maps the keys
     */
    public function __construct(\Traversable $iterator, callable $valueMapper, callable $keyMapper = null)
    {
        parent::__construct($iterator);
        $this->valueMapper = lang\ensureCallable($valueMapper);
        $this->keyMapper   = null !== $keyMapper ? lang\ensureCallable($keyMapper) : (null);
    }

    /**
     * returns the current element
     *
     * @return  string
     */
    public function current()
    {
        $map = $this->valueMapper;
        return $map(parent::current(), parent::key());
    }

    /**
     * returns the current key
     *
     * @return  mixed
     */
    public function key()
    {
        if (null === $this->keyMapper) {
            return parent::key();
        }

        $map = $this->keyMapper;
        return $map(parent::key());
    }
}
