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
 * @since  6.0.0
 */
class BsdSocketConnection
{
    /**
     * internal resource pointer
     *
     * @type  resource
     */
    private $resource;
    /**
     * switch whether end of socket was reached or not
     *
     * @type  bool
     */
    private $eof      = false;

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
    public function __construct($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) !== 'socket') {
            throw new \InvalidArgumentException('Given resource is not a socket');
        }

        $this->resource = $resource;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        // on unit tests resource might be closed from outside
        if (is_resource($this->resource)) {
            socket_close($this->resource);
        }
    }

    /**
     * sets a lis of options on the connection
     *
     * @param   array  $options
     * @return  $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $level => $pairs) {
            foreach ($pairs as $name => $value) {
                $this->setOption($level, $name, $value);
            }
        }

        return $this;
    }

    /**
     * sets an option
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @param   mixed  $value  option value
     * @return  $this
     */
    public function setOption($level, $name, $value)
    {
        if (!socket_set_option($this->resource, $level, $name, $value)) {
            throw new ConnectionException(
                    'Failed to set option ' . $name
                    . ' on level ' . $level . ' to value ' . $value
            );
        }
        return $this;
    }

    /**
     * returns an option
     *
     * @param   int  $level  protocol level of option
     * @param   int  $name   option name
     * @return  mixed
     * @throws  \stubbles\peer\ConnectionException
     */
    public function option($level, $name)
    {
        $option = socket_get_option($this->resource, $level, $name);
        if (false === $option) {
            throw new ConnectionException(
                    'Failed to retrieve option ' . $name . ' on level ' . $level
            );
        }

        return $option;
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
     * @throws  \LogicException
     */
    public function read($length = 4096)
    {
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
     * @throws  \LogicException
     */
    public function readBinary($length = 1024)
    {
        return $this->doRead($length, PHP_BINARY_READ);
    }

    /**
     * write data to socket and returns the amount of written bytes
     *
     * @param   string  $data  data to write
     * @return  int
     * @throws  \stubbles\peer\ConnectionException
     * @throws  \LogicException
     */
    public function write($data)
    {
        $length = socket_write($this->resource, $data, strlen($data));
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
        $result = socket_read($this->resource, $length, $type);
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
