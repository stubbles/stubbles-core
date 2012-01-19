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
use net\stubbles\streams\OutputStream;
/**
 * Output stream for writing to sockets.
 */
class SocketOutputStream extends BaseObject implements OutputStream
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
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->socket->write($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->socket->write($bytes . "\r\n");
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