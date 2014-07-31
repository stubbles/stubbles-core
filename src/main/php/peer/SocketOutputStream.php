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
use stubbles\streams\OutputStream;
/**
 * Output stream for writing to sockets.
 *
 * @api
 */
class SocketOutputStream implements OutputStream
{
    /**
     * socket to read from
     *
     * @type  \stubbles\peer\Socket
     */
    protected $socket;

    /**
     * constructor
     *
     * @param  \stubbles\peer\Socket  $socket
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
     * @return  int     amount of written bytes
     */
    public function writeLine($bytes)
    {
        return $this->socket->write($bytes . "\r\n");
    }

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
     * @since   3.2.0
     */
    public function writeLines(array $bytes)
    {
        $bytesWritten = 0;
        foreach ($bytes as $line) {
            $bytesWritten += $this->writeLine($line);
        }

        return $bytesWritten;
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->socket->disconnect();
    }
}
