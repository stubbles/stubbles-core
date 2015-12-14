<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
use stubbles\lang\exception\IOException;
/**
 * Input stream for reading from php://input.
 *
 * @since  5.4.0
 */
class StandardInputStream extends ResourceInputStream implements Seekable
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setHandle(fopen('php://input', 'rb'));
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset  offset to seek to
     * @param   int  $whence  optional  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  \LogicException  in case the stream was already closed
     */
    public function seek($offset, $whence = Seekable::SET)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        fseek($this->handle, $offset, $whence);
    }

    /**
     * return current position
     *
     * @return  int
     * @throws  \LogicException  in case the stream was already closed
     * @throws  \stubbles\lang\exception\IOException
     */
    public function tell()
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream');
        }

        $position = ftell($this->handle);
        if (false === $position) {
            throw new IOException('Can not read current position in php://input');
        }

        return $position;
    }
}
