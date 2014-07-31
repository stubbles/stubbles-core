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
use stubbles\lang\Enum;
/**
 * Enum for different socket domains.
 *
 * @since  2.0.0
 */
class SocketDomain extends Enum
{
    /**
     * domain type inet
     *
     * The host on connect() can either by an IPv4 address or a host name.
     *
     * Requires a port to connect.
     *
     * @api
     * @type  \stubbles\peer\SocketDomain
     */
    public static $AF_INET;
    /**
     * domain type inet with ipv6
     *
     * Please note that the host on connect() must be an IPv6 address, a host
     * name is not sufficient.
     *
     * Requires a port to connect.
     *
     * @api
     * @type  \stubbles\peer\SocketDomain
     */
    public static $AF_INET6;
    /**
     * domain type local file socket
     *
     * The host() on connect() must be the path name of a unix domain socket.
     *
     * Does not require a port to connect.
     *
     * @api
     * @type  \stubbles\peer\SocketDomain
     */
    public static $AF_UNIX;
    /**
     * function to connect with this domain type
     *
     * @type  \Closure
     */
    private $connect;
    /**
     * switch whether port is required or not
     *
     * @type  bool
     */
    private $portRequired;
    /**
     * @type  bool
     */
    const PORT_REQUIRED     = true;
    /**
     * @type  bool
     */
    const PORT_NOT_REQUIRED = false;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$AF_INET  = new self('AF_INET',
                                   AF_INET,
                                   function($fp, $host, $port) { return socket_connect($fp, gethostbyname($host), $port); },
                                   self::PORT_REQUIRED
                          );
        self::$AF_INET6 = new self('AF_INET6',
                                   AF_INET6,
                                   function($fp, $host, $port) { return socket_connect($fp, $host, $port); },
                                   self::PORT_REQUIRED
                          );
        self::$AF_UNIX  = new self('AF_UNIX',
                                   AF_UNIX,
                                   function($fp, $host) { return socket_connect($fp, $host); },
                                   self::PORT_NOT_REQUIRED
                          );
    }

    /**
     * constructor
     *
     * @param  string    $name
     * @param  int       $value
     * @param  \Closure  $connect
     * @param  bool      $portRequired
     */
    protected function __construct($name, $value, \Closure $connect, $portRequired)
    {
        parent::__construct($name, $value);
        $this->connect      = $connect;
        $this->portRequired = $portRequired;
    }

    /**
     * Connects to the given host and port using the resource pointer.
     *
     * Returns the resource pointer on successful connect.
     *
     * @param   resource  $fp
     * @param   string    $host
     * @param   int       $port
     * @return  resource
     * @throws  \stubbles\peer\ConnectionException
     */
    public function connect($fp, $host, $port)
    {
        $connect = $this->connect;
        if (!$connect($fp, $host, $port)) {
            $e = socket_last_error($fp);
            throw new ConnectionException('Connect to ' . $host . ':' .$port . ' failed: ' . $e . ': ' . socket_strerror($e));
        }

        return $fp;
    }

    /**
     * checks whether this domain requires a port or not
     *
     * @return  bool
     */
    public function requiresPort()
    {
        return $this->portRequired;
    }
}
SocketDomain::__static();
