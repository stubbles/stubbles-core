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
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\IllegalStateException;
/**
 * Class for operations on bsd-style sockets.
 *
 * @api
 */
class BsdSocket extends Socket
{
    /**
     * switch whether end of socket was reached or not
     *
     * @type  bool
     */
    protected $eof          = true;
    /**
     * domain type
     *
     * @type  SocketDomain
     */
    protected $domain;
    /**
     * type of socket
     *
     * @type  int
     */
    protected $type         = SOCK_STREAM;
    /**
     * protocol to use: tcp or udp
     *
     * @type  int
     */
    protected $protocol     = SOL_TCP;
    /**
     * list of options for the socket
     *
     * @type  \stubbles\peer\SocketOptions
     */
    protected $options;
    /**
     * list of available socket types
     *
     * @type  array
     */
    protected static $types = [SOCK_STREAM    => 'SOCK_STREAM',
                               SOCK_DGRAM     => 'SOCK_DGRAM',
                               SOCK_RAW       => 'SOCK_RAW',
                               SOCK_SEQPACKET => 'SOCK_SEQPACKET',
                               SOCK_RDM       => 'SOCK_RDM'
                              ];

    /**
     * constructor
     *
     * Port can be null for SocketDomain::$AF_UNIX, all other domains require
     * a port.
     *
     * @param   \stubbles\peer\SocketDomain  $domain   one of SocketDomain::$AF_INET, SocketDomain::$AF_INET6 or SocketDomain::$AF_UNIX
     * @param   string                       $host     host to connect socket to
     * @param   int                          $port     optional  port to connect socket to, defaults to 80
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function __construct(SocketDomain $domain, $host, $port = null)
    {
        if ($domain->requiresPort() && empty($port)) {
            throw new IllegalArgumentException('Domain ' . $domain->name() . ' requires a port');
        }

        parent::__construct($host, $port);
        $this->domain  = $domain;
        $this->options = new SocketOptions();
    }

    /**
     * returns the domain
     *
     * @return  \stubbles\peer\SocketDomain
     * @deprecated  since 4.0.0, will be removed with 5.0.0
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * sets the socket type
     *
     * @param   int  $type  one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @return  \stubbles\peer\BsdSocket
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(self::$types))) {
            throw new IllegalArgumentException('Type must be one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM.');
        }

        if ($this->isConnected()) {
            throw new IllegalStateException('Can not change type on already connected socket.');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * returns the socket type
     *
     * @return  int
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * returns the socket type
     *
     * @return  int
     * @deprecated  since 4.0.0, use type() instead, will be removed with 5.0.0
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * enables tcp protocol
     *
     * @return  \stubbles\peer\BsdSocket
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function useTcp()
    {
        if ($this->isConnected()) {
            throw new IllegalStateException('Can not change protocol on already connected socket.');
        }

        $this->protocol = SOL_TCP;
        return $this;
    }

    /**
     * checks whether socket uses tcp
     *
     * @return  bool
     */
    public function isTcp()
    {
        return SOL_TCP === $this->protocol;
    }

    /**
     * enables udp protocol
     *
     * @return  \stubbles\peer\BsdSocket
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function useUdp()
    {
        if ($this->isConnected()) {
            throw new IllegalStateException('Can not change protocol on already connected socket.');
        }

        $this->protocol = SOL_UDP;
        return $this;
    }

    /**
     * checks whether socket uses udp
     *
     * @return  bool
     */
    public function isUdp()
    {
        return SOL_UDP === $this->protocol;
    }

    /**
     * sets an option
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @param   mixed  $value  option value
     * @return  \stubbles\peer\BsdSocket
     */
    public function setOption($level, $name, $value)
    {
        $this->options->set($level, $name, $value);
        return $this;
    }

    /**
     * returns an option
     *
     * @param   int    $level    protocol level of option
     * @param   int    $name     option name
     * @param   mixed  $default  value to return if option not set
     * @return  mixed
     */
    public function option($level, $name, $default)
    {
        return $this->options->get($level, $name, $default);
    }

    /**
     * returns an option
     *
     * @param   int    $level    protocol level of option
     * @param   int    $name     option name
     * @param   mixed  $default  value to return if option not set
     * @return  mixed
     * @deprecated  since 4.0.0, use option() instead, will be removed with 5.0.0
     */
    public function getOption($level, $name, $default)
    {
        return $this->options->get($level, $name, $default);
    }

    /**
     * opens a socket connection
     *
     * @param   int  $connectTimeout  timeout for establishing the connection
     * @return  \stubbles\peer\BsdSocket
     * @throws  \stubbles\peer\ConnectionException
     */
    public function connect($connectTimeout = 2)
    {
        if ($this->isConnected()) {
            return $this;
        }

        $fp = @socket_create($this->domain->value(), $this->type, $this->protocol);
        if (false === $fp) {
            throw new ConnectionException(sprintf('Create of %s socket (type %s, protocol %s) failed.',
                                                  $this->domain->name(),
                                                  self::$types[$this->type],
                                                  getprotobynumber($this->protocol)
                                          )
                      );
        }

        $this->options->bindToConnection($fp);
        $this->fp  = $this->domain->connect($fp, $this->host, $this->port);
        $this->eof = false;
        return $this;
    }

    /**
     * closes a connection
     *
     * @return  \stubbles\peer\BsdSocket
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            socket_close($this->fp);
        }

        return $this;
    }

    /**
     * returns last error
     *
     * @return  string
     */
    public function lastError()
    {
        $e = socket_last_error($this->fp);
        return $e . ': ' . socket_strerror($e);
    }

    /**
     * read from socket
     *
     * @param   int  $length  length of data to read
     * @return  string
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function read($length = 4096)
    {
        if ($this->isConnected() == false) {
            throw new IllegalStateException('Can not read on unconnected socket.');
        }

        return $this->doRead($length, PHP_NORMAL_READ);
    }

    /**
     * read a whole line from socket
     *
     * @param   int  $length  length of data to read
     * @return  string
     */
    public function readLine($length = 4096)
    {
        return rtrim($this->read($length));
    }

    /**
     * read binary data from socket
     *
     * @param   int  $length  length of data to read
     * @return  string
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function readBinary($length = 1024)
    {
        if ($this->isConnected() == false) {
            throw new IllegalStateException('Can not read on unconnected socket.');
        }

        return $this->doRead($length, PHP_BINARY_READ);
    }

    /**
     * write data to socket and returns the amount of written bytes
     *
     * @param   string  $data  data to write
     * @return  int
     * @throws  \stubbles\peer\ConnectionException
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function write($data)
    {
        if ($this->isConnected() == false) {
            throw new IllegalStateException('Can not write on unconnected socket.');
        }

        $length = socket_write($this->fp, $data, strlen($data));
        if (false === $length) {
            throw new ConnectionException('"Writing of ' . strlen($data) . ' bytes failed.');
        }

        return $length;
    }

    /**
     * helper method to do the actual reading
     *
     * @param   int  $length  length of data to read
     * @param   int  $type    one of PHP_BINARY_READ or PHP_NORMAL_READ
     * @return  string
     * @throws  \stubbles\peer\ConnectionException
     */
    protected function doRead($length, $type)
    {
        $result = socket_read($this->fp, $length, $type);
        if (false === $result) {
            throw new ConnectionException('Read failed: ' . $this->lastError());
        }

        if (empty($result)) {
            $this->eof = true;
            $result = null;
        }

        return $result;
    }

    /**
     * check if we reached end of data
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->eof;
    }
}
