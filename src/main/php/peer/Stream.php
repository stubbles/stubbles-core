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
 * Class for operations on socket/stream connections.
 *
 * @api
 * @since  6.0.0
 */
class Stream
{
    /**
     * internal resource pointer
     *
     * @type  resource
     */
    private $resource;
    /**
     * timeout
     *
     * @type  int
     */
    private $timeout;
    /**
     * input stream to read data from stream with
     *
     * @type  \stubbles\streams\InputStream
     */
    private $inputStream;
    /**
     * output stream to read data from stream with
     *
     * @type  \stubbles\streams\OutputStream
     */
    private $outputStream;

    /**
     * constructor
     *
     * @param   resource  $resource  actual socket resource
     * @param   int       $timeout  connection timeout
     * @throws  \InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) !== 'stream') {
            throw new \InvalidArgumentException('Given resource is not a socket stream');
        }

        $this->resource = $resource;
        $this->timeout  = ini_get('default_socket_timeout');
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        // on unit tests resource might be closed from outside
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * set timeout for connections
     *
     * @param   int  $seconds       timeout for connection in seconds
     * @param   int  $microseconds  optional  timeout for connection in microseconds
     * @return  $this
     */
    public function setTimeout($seconds, $microseconds = 0)
    {
        $this->timeout = $seconds . '.' . $microseconds;
        stream_set_timeout($this->resource, $seconds, $microseconds);
        return $this;
    }

    /**
     * returns current timeout setting
     *
     * @return  float
     */
    public function timeout()
    {
        return $this->timeout;
    }

    /**
     * read from socket
     *
     * @param   int  $length  length of data to read
     * @return  string  data read from socket
     * @throws  \stubbles\peer\ConnectionException
     * @throws  \stubbles\peer\Timeout
     */
    public function read($length = 4096)
    {
        $data = fgets($this->resource, $length);
        if (false === $data) {
            // fgets() returns false on eof while feof() returned false before
            // but will now return true
            if ($this->eof()) {
                return null;
            }

            if (stream_get_meta_data($this->resource)['timed_out']) {
                throw new Timeout(
                        'Reading of ' . $length . ' bytes failed: timeout of '
                        . $this->timeout . ' seconds exceeded'
                );
            }

            throw new ConnectionException(
                    'Reading of ' . $length . ' bytes failed.'
            );
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
     * @throws  \stubbles\peer\ConnectionException
     * @throws  \stubbles\peer\Timeout
     */
    public function readBinary($length = 1024)
    {
        $data = fread($this->resource, $length);
        if (false === $data) {
            if (stream_get_meta_data($this->resource)['timed_out']) {
                throw new Timeout(
                        'Reading of ' . $length . ' bytes failed: timeout of '
                        . $this->timeout . ' seconds exceeded'
                );
            }

            throw new ConnectionException(
                    'Reading of ' . $length . ' bytes failed.'
            );
        }

        return $data;
    }

    /**
     * write data to socket
     *
     * @param   string  $data  data to write
     * @return  int  amount of bytes written to socket
     * @throws  \stubbles\peer\ConnectionException
     * @throws  \stubbles\peer\Timeout
     */
    public function write($data)
    {
        $length = fputs($this->resource, $data, strlen($data));
        if (false === $length) {
            if (stream_get_meta_data($this->resource)['timed_out']) {
                throw new Timeout(
                        'Writing of ' . strlen($data) . ' bytes failed:'
                        . ' timeout of ' . $this->timeout . ' seconds exceeded'
                );
            }

            throw new ConnectionException(
                    'Writing of ' . strlen($data) . ' bytes failed.'
            );
        }

        return $length;
    }

    /**
     * check if we reached end of data
     *
     * @return  bool
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * returns input stream to read from socket
     *
     * @return  \stubbles\streams\InputStream
     */
    public function in()
    {
        if (null === $this->inputStream) {
            $this->inputStream = new SocketInputStream($this);
        }

        return $this->inputStream;
    }

    /**
     * returns output stream to write to socket
     *
     * @return  \stubbles\streams\OutputStream
     */
    public function out()
    {
        if (null === $this->outputStream) {
            $this->outputStream = new SocketOutputStream($this);
        }

        return $this->outputStream;
    }
}
