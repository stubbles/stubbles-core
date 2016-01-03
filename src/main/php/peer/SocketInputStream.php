<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
use stubbles\streams\InputStream;
/**
 * Input stream for reading sockets.
 *
 * @api
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class SocketInputStream implements InputStream
{
    /**
     * socket to read from
     *
     * @type  \stubbles\peer\SocketConnection
     */
    private $socket;

    /**
     * constructor
     *
     * @param  \stubbles\peer\Stream  $socket
     */
    public function __construct(Stream $socket)
    {
        $this->socket = $socket;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     */
    public function read($length = 8192)
    {
        if (null === $this->socket) {
            throw new \LogicException('Can not read from closed socket');
        }

        return $this->socket->readBinary($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     */
    public function readLine($length = 8192)
    {
        if (null === $this->socket) {
            throw new \LogicException('Can not read from closed socket');
        }

        return $this->socket->readLine($length);
    }

    /**
     * returns the amount of bytes left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        if ($this->eof()) {
            return -1;
        }

        // we never know how much bytes are left, so we simply say it's at
        // least one byte
        return 1;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        if (null === $this->socket) {
            return true;
        }

        return $this->socket->eof();
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->socket = null;
    }
}
