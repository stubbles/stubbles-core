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
 * Provides the possibility to iterate over the data a class contains when it is backed by properties.
 *
 * @since  5.0.0
 */
trait PropertyBasedIterator
{
    /**
     * returns the properties where we iterate on
     *
     * @return  \stubbles\lang\Properties
     */
    protected abstract function properties();

    /**
     * returns current entry in iteration
     *
     * Most likely you want to override this method to return some structure the
     * class is thought to return, not the list of properties of the current
     * section.
     *
     * @return  mixed
     */
    public function current()
    {
        return $this->properties()->current();
    }

    /**
     * returns current section key in iteration
     *
     * @return  string
     */
    public function key()
    {
        return $this->properties()->key();
    }

    /**
     * advances iteration to next element
     */
    public function next()
    {
        $this->properties()->next();
    }

    /**
     * rewind iteration to first element
     */
    public function rewind()
    {
        $this->properties()->rewind();
    }

    /**
     * checks if current entry is valid
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->properties()->valid();
    }
}
