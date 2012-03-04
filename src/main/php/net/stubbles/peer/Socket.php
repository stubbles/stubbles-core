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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\IllegalStateException;
/**
 * Class for operations on sockets.
 *
 * @api
 */
class Socket extends BaseObject
{
    /**
     * host to open socket to
     *
     * @type  string
     */
    protected $host;
    /**
     * port to use for opening the socket
     *
     * @type  int
     */
    protected $port;
    /**
     * prefix for host, e.g. ssl://
     *
     * @type  string
     */
    protected $prefix;
    /**
     * timeout
     *
     * @type  int
     */
    protected $timeout;
    /**
     * internal resource pointer
     *
     * @type  resource
     */
    protected $fp;

    /**
     * constructor
     *
     * @param   string  $host     host to open socket to
     * @param   int     $port     port to use for opening the socket
     * @param   string  $prefix   prefix for host, e.g. ssl://
     * @param   int     $timeout  connection timeout
     * @throws  IllegalArgumentException
     */
    public function __construct($host, $port = 80, $prefix = null, $timeout = 5)
    {
        if (empty($host)) {
            throw new IllegalArgumentException('Host can not be empty');
        }

        $this->host    = $host;
        $this->port    = $port;
        $this->prefix  = $prefix;
        $this->timeout = $timeout;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * opens a connection to host
     *
     * @param   int  $connectTimeout  timeout for establishing the connection
     * @return  bool  true if connect was successful
     * @throws  ConnectionException
     */
    public function connect($connectTimeout = 2)
    {
        if ($this->isConnected()) {
            return true;
        }

        $errno    = 0;
        $errstr   = '';
        $this->fp = @fsockopen($this->prefix . $this->host, $this->port, $errno, $errstr, $connectTimeout);
        if (false === $this->fp) {
            $this->fp = null;
            throw new ConnectionException('Connecting to ' . $this->prefix . $this->host . ':' . $this->port . ' within ' . $connectTimeout . ' seconds failed: ' . $errstr . ' (' . $errno . ').');
        }

        socket_set_timeout($this->fp, $this->timeout);
        return true;
    }

    /**
     * closes a connection
     *
     * @return  Socket
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            fclose($this->fp);
            $this->fp = null;
        }

        return $this;
    }

    /**
     * set timeout for connections
     *
     * @param   int  $timeout  timeout for connection in seconds
     * @return  Socket
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        if ($this->isConnected()) {
            socket_set_timeout($this->fp, $this->timeout);
        }

        return $this;
    }

    /**
     * read from socket
     *
     * @param   int  $length  length of data to read
     * @return  string  data read from socket
     * @throws  ConnectionException
     * @throws  IllegalStateException
     */
    public function read($length = 4096)
    {
        if (!$this->isConnected()) {
            throw new IllegalStateException('Can not read on unconnected socket.');
        }

        $data = fgets($this->fp, $length);
        if (false === $data) {
            // fgets returns false on eof while feof() returned false before
            // but will now return true
            if ($this->eof()) {
                return null;
            }

            throw new ConnectionException('Reading of ' . $length . ' bytes failed.');
        }

        return $data;
    }

    /**
     * read a whole line from socket
     *
     * @param   int  $length  length of data to read
     * @return  string  data read from socket
     */
    public function readLine($length = 4096)
    {
        return rtrim($this->read($length));
    }

    /**
     * read binary data from socket
     *
     * @param   int  $length  length of data to read
     * @return  string  data read from socket
     * @throws  ConnectionException
     * @throws  IllegalStateException
     */
    public function readBinary($length = 1024)
    {
        if (!$this->isConnected()) {
            throw new IllegalStateException('Can not read on unconnected socket.');
        }

        $data = fread($this->fp, $length);
        if (false === $data) {
            throw new ConnectionException('Reading of ' . $length . ' bytes failed.');
        }

        return $data;
    }

    /**
     * write data to socket
     *
     * @param   string  $data  data to write
     * @return  int  amount of bytes written to socket
     * @throws  ConnectionException
     * @throws  IllegalStateException
     */
    public function write($data)
    {
        if (!$this->isConnected()) {
            throw new IllegalStateException('Can not write on unconnected socket.');
        }

        $length = fputs($this->fp, $data, strlen($data));
        if (false === $length) {
            throw new ConnectionException('"Writing of ' . strlen($data) . ' bytes failed.');
        }

        return $length;
    }

    /**
     * get host of current connection
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * get port of current connection
     *
     * @return  int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * returns prefix for host, e.g. ssl://
     *
     * @return  string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * get timeout for connections
     *
     * @return  int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * checks if we already have a connection
     *
     * @return  bool
     */
    public function isConnected()
    {
        return is_resource($this->fp);
    }

    /**
     * check if we reached end of data
     *
     * @return  bool
     */
    public function eof()
    {
        if ($this->isConnected()) {
            return feof($this->fp);
        }

        return true;
    }

    /**
     * returns input stream to read from socket
     *
     * @return InputStream
     * @since  2.0.0
     */
    public function getInputStream()
    {
        return new SocketInputStream($this);
    }

    /**
     * returns output stream to write to socket
     *
     * @return OutputStream
     * @since  2.0.0
     */
    public function getOutputStream()
    {
        return new SocketOutputStream($this);
    }
}
?>