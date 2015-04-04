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
 * Class for operations on bsd-style sockets.
 *
 * @api
 */
class BsdSocket
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
     * domain type
     *
     * @type  \stubbles\peer\SocketDomain
     */
    private $domain;
    /**
     * type of socket
     *
     * @type  int
     */
    private $type         = SOCK_STREAM;
    /**
     * protocol to use: tcp or udp
     *
     * @type  int
     */
    private $protocol     = SOL_TCP;
    /**
     * list of options for the socket
     *
     * @type  array
     */
    private $options      = [];
    /**
     * list of available socket types
     *
     * @type  array
     */
    private static $types = [SOCK_STREAM    => 'SOCK_STREAM',
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
     * @throws  \InvalidArgumentException
     */
    public function __construct(SocketDomain $domain, $host, $port = null)
    {
        if ($domain->requiresPort() && empty($port)) {
            throw new \InvalidArgumentException(
                    'Domain ' . $domain->name() . ' requires a port'
            );
        }

        $this->host   = $host;
        $this->port   = $port;
        $this->domain = $domain;
    }

    /**
     * sets the socket type
     *
     * @param   int  $type  one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @return  \stubbles\peer\BsdSocket
     * @throws  \InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(self::$types))) {
            throw new \InvalidArgumentException(
                    'Type must be one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW,'
                    . ' SOCK_SEQPACKET or SOCK_RDM.'
            );
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
     * enables tcp protocol
     *
     * @return  \stubbles\peer\BsdSocket
     */
    public function useTcp()
    {
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
     */
    public function useUdp()
    {
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
        if (!isset($this->options[$level])) {
            $this->options[$level] = [];
        }

        $this->options[$level][$name] = $value;
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
        if (isset($this->options[$level]) && isset($this->options[$level][$name])) {
            return $this->options[$level][$name];
        }

        return $default;
    }

    /**
     * opens a socket connection
     *
     * @return  \stubbles\peer\BsdSocketConnection
     * @throws  \stubbles\peer\ConnectionException
     */
    public function connect()
    {
        $socket = @socket_create(
                $this->domain->value(),
                $this->type,
                $this->protocol
        );
        if (false === $socket) {
            throw new ConnectionException(
                    sprintf(
                            'Create of %s socket (type %s, protocol %s) failed.',
                            $this->domain->name(),
                            self::$types[$this->type],
                            getprotobynumber($this->protocol)
                    )
            );
        }

        $connection = new BsdSocketConnection(
                $this->domain->connect($socket, $this->host, $this->port)
        );
        return $connection->setOptions($this->options);
    }
}
