<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\memory;
use net\stubbles\lang\BaseObject;
use net\stubbles\streams\StreamFactory;
/**
 * Factory for memory streams.
 */
class MemoryStreamFactory extends BaseObject implements StreamFactory
{
    /**
     * creates an input stream for given source
     *
     * @param   mixed  $source   source to create input stream from
     * @param   array  $options  list of options for the input stream
     * @return  MemoryInputStream
     */
    public function createInputStream($source, array $options = array())
    {
        return new MemoryInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param   mixed  $target   target to create output stream for
     * @param   array  $options  list of options for the output stream
     * @return  MemoryOutputStream
     */
    public function createOutputStream($target, array $options = array())
    {
        return new MemoryOutputStream();
    }
}
?>