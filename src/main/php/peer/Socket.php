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
/**
 * Represents a socket to which a connection can be established.
 *
 * @api
 */
class Socket
{
    /**
     * host to open socket to
     *
     * @type  string
     */
    private $host;
    /**
     * port to use for opening the socket
     *
     * @type  int
     */
    private $port;
    /**
     * prefix for host, e.g. ssl://
     *
     * @type  string
     */
    private $prefix;

    /**
     * constructor
     *
     * @param   string  $host    host to open socket to
     * @param   int     $port    port to use for opening the socket
     * @param   string  $prefix  prefix for host, e.g. ssl://
     * @throws  \InvalidArgumentException
     */
    public function __construct($host, $port = 80, $prefix = null)
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('Host can not be empty');
        }

        if (0 > $port) {
            throw new \InvalidArgumentException('Port can not be negative');
        }

        $this->host   = $host;
        $this->port   = $port;
        $this->prefix = $prefix;
    }

    /**
     * opens a connection to host
     *
     * @param   float  $connectTimeout  optional timeout for establishing the connection, defaults to 1 second
     * @return  \stubbles\peer\Stream
     * @throws  \stubbles\peer\ConnectionException
     */
    public function connect($connectTimeout = 1.0)
    {
        $errno  = 0;
        $errstr = '';
        $resource = fsockopen(
                $this->prefix . $this->host,
                $this->port,
                $errno,
                $errstr,
                $connectTimeout
        );
        if (false === $resource) {
            throw new ConnectionException(
                    'Connect to ' . $this->prefix . $this->host . ':'. $this->port
                    . ' within ' . $connectTimeout . ' second'
                    . (1 == $connectTimeout ? '' : 's') . ' failed: '
                    . $errstr . ' (' . $errno . ').'
            );
        }

        return new Stream($resource);
    }

    /**
     * checks if socket uses a secure connection
     *
     * @return  bool
     * @since   4.0.0
     */
    public function usesSsl()
    {
        return 'ssl://' === $this->prefix || 'tls://' === $this->prefix;
    }
}
