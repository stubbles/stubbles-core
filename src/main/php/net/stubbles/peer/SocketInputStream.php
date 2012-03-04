<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
use net\stubbles\lang\BaseObject;
use net\stubbles\streams\InputStream;
/**
 * Input stream for reading sockets.
 *
 * @api
 */
class SocketInputStream extends BaseObject implements InputStream
{
    /**
     * socket to read from
     *
     * @type  Socket
     */
    protected $socket;

    /**
     * constructor
     *
     * @param  Socket  $socket
     */
    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
        $this->socket->connect();
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return $this->socket->readBinary($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return $this->socket->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        if ($this->socket->eof()) {
            return -1;
        }

        return 1;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->socket->eof();
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->socket->disconnect();
    }
}
?>