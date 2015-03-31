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
     * Please note that seeking on php://input is only supported since PHP 5.6.
     * In case the seek is done to offset 0 with Seekable::SET and PHP version
     * is below PHP 5.6 it will simply reopen the underlying resource. In any
     * other case a BadMethodCallException will be thrown.
     *
     * @param   int  $offset  offset to seek to
     * @param   int  $whence  optional  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  \LogicException  in case the stream was already closed
     * @throws  \BadMethodCallException
     */
    public function seek($offset, $whence = Seekable::SET)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
            fseek($this->handle, $offset, $whence);
        } elseif (Seekable::SET === $whence && 0 === $offset) {
            $this->setHandle(fopen('php://input', 'r'));
        } else {
            throw new \BadMethodCallException('Seeking on php://input in versions prior to PHP 5.6 is not possible');
        }
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
