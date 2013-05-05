<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer {
    use \net\stubbles\peer\HeaderList;
    use \net\stubbles\peer\SocketDomain;
    use \net\stubbles\peer\http\HttpUri;

    /**
     * creates a http connection to specified uri
     * @param   string                         $uri
     * @param   \net\stubbles\peer\HeaderList  $headers
     * @return  \net\stubbles\peer\http\HttpConnection
     * @since   3.1.0
     * @api
     */
    function http($uri, HeaderList $headers = null)
    {
        return HttpUri::fromString($uri)
                      ->connect($headers);
    }

    /**
     * creates a list of headers from given map
     *
     * @param   array  $headers
     * @return  \net\stubbles\peer\HeaderList
     * @since   3.1.0
     * @api
     */
    function headers(array $headers = array())
    {
        return new HeaderList($headers);
    }

    /**
     * creates a list of headers from given header string
     *
     * @param   array  $headers
     * @return  \net\stubbles\peer\HeaderList
     * @since   3.1.0
     * @api
     */
    function parseHeaders($headers)
    {
        return HeaderList::fromString($headers);
    }

    /**
     * creates a new socket
     *
     * @param   string  $host     host to open socket to
     * @param   int     $port     port to use for opening the socket
     * @param   string  $prefix   prefix for host, e.g. ssl://
     * @param   int     $timeout  connection timeout
     * @return  \net\stubbles\peer\Socket
     * @since   3.1.0
     * @api
     */
    function createSocket($host, $port = 80, $prefix = null, $timeout = 5)
    {
        return new \net\stubbles\peer\Socket($host, $port, $prefix, $timeout);
    }

    /**
     * creates a new bsd socket
     *
     * Port can be null for SocketDomain::$AF_UNIX, all other domains require
     * a port.
     *
     * @param   SocketDomain   $domain   one of SocketDomain::$AF_INET, SocketDomain::$AF_INET6 or SocketDomain::$AF_UNIX
     * @param   string         $host     host to connect socket to
     * @param   int            $port     port to connect socket to
     * @return  \net\stubbles\peer\BsdSocket
     * @since   3.1.0
     * @api
     */
    function createBsdSocket(SocketDomain $domain, $host, $port = null)
    {
        return new \net\stubbles\peer\BsdSocket($domain, $host, $port);
    }
}
?>