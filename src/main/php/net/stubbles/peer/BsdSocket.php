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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\IllegalStateException;
/**
 * Class for operations on bsd-style sockets.
 */
class BsdSocket extends Socket
{
    /**
     * switch whether end of socket was reached or not
     *
     * @type  bool
     */
    protected $eof            = true;
    /**
     * domain type
     *
     * @type  string
     */
    protected $domain         = AF_INET;
    /**
     * type of socket
     *
     * @type  int
     */
    protected $type           = SOCK_STREAM;
    /**
     * protocol to use: tcp or udp
     *
     * @type  int
     */
    protected $protocol       = SOL_TCP;
    /**
     * list of options for the socket
     *
     * @type  array
     */
    protected $options        = array();
    /**
     * list of available domains
     *
     * @type  array
     */
    protected static $domains = array(AF_INET  => 'AF_INET',
                                      AF_INET6 => 'AF_INET6',
                                      AF_UNIX  => 'AF_UNIX'
                                );
    /**
     * list of available socket types
     *
     * @type  array
     */
    protected static $types   = array(SOCK_STREAM    => 'SOCK_STREAM',
                                      SOCK_DGRAM     => 'SOCK_DGRAM',
                                      SOCK_RAW       => 'SOCK_RAW',
                                      SOCK_SEQPACKET => 'SOCK_SEQPACKET',
                                      SOCK_RDM       => 'SOCK_RDM'
                                );

    /**
     * sets the domain
     *
     * @param   int  $domain  one of AF_INET, AF_INET6 or AF_UNIX
     * @return  BsdSocket
     * @throws  IllegalArgumentException
     * @throws  IllegalStateException
     */
    public function setDomain($domain)
    {
        if (in_array($domain, array_keys(self::$domains)) === false) {
            throw new IllegalArgumentException('Domain must be one of AF_INET, AF_INET6 or AF_UNIX.');
        }

        if ($this->isConnected() === true) {
            throw new IllegalStateException('Can not change domain on already connected socket.');
        }

        $this->domain = $domain;
        return $this;
    }

    /**
     * returns the domain
     *
     * @return  int
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * sets the socket type
     *
     * @param   int  $type  one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @return  BsdSocket
     * @throws  IllegalArgumentException
     * @throws  IllegalStateException
     */
    public function setType($type)
    {
        if (in_array($type, array_keys(self::$types)) === false) {
            throw new IllegalArgumentException('Type must be one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM.');
        }

        if ($this->isConnected() === true) {
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * enables tcp protocol
     *
     * @return  BsdSocket
     * @throws  IllegalStateException
     */
    public function useTcp()
    {
        if ($this->isConnected() === true) {
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
     * @return  BsdSocket
     * @throws  IllegalStateException
     */
    public function useUdp()
    {
        if ($this->isConnected() === true) {
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
     * @return  BsdSocket
     * @throws  ConnectionException
     */
    public function setOption($level, $name, $value)
    {
        if (isset($this->options[$level]) === false) {
            $this->options[$level] = array();
        }

        $this->options[$level][$name] = $value;
        if ($this->isConnected() === true) {
            if (socket_set_option($this->fp, $level, $name, $value) === false) {
                throw new ConnectionException('Failed to set option ' . $name . ' on level ' . $level . ' to value ' . $value);
            }
        }

        return $this;
    }

    /**
     * returns an option
     *
     * @param   int  $level  protocol level of option
     * @param   int  $name   option name
     * @return  mixed
     * @throws  ConnectionException
     */
    public function getOption($level, $name)
    {
        if ($this->isConnected() === true) {
            $option = socket_get_option($this->fp, $level, $name);
            if (false === $option) {
                throw new ConnectionException('Failed to retrieve option ' . $name . ' on level ' . $level);
            }

            if (isset($this->options[$level]) === false) {
                $this->options[$level] = array();
            }

            $this->options[$level][$name] = $option;
        }

        if (isset($this->options[$level]) === true && isset($this->options[$level][$name]) === true) {
            return $this->options[$level][$name];
        }

        return null;
    }

    /**
     * opens a socket connection
     *
     * @param   int  $connectTimeout  timeout for establishing the connection
     * @return  bool  true if connect was successful
     * @throws  ConnectionException
     */
    public function connect($connectTimeout = 2)
    {
        if ($this->isConnected() === true) {
            return true;
        }

        $this->fp = @socket_create($this->domain, $this->type, $this->protocol);
        if (false === $this->fp) {
            $this->fp = null;
            throw new ConnectionException(sprintf('Create of %s socket (type %s, protocol %s) failed.',
                                                  self::$domains[$this->domain],
                                                  self::$types[$this->type],
                                                  getprotobynumber($this->protocol)
                                          )
                      );
        }

        foreach ($this->options as $level => $pairs) {
            foreach ($pairs as $name => $value) {
                socket_set_option($this->fp, $level, $name, $value);
            }
        }

        switch ($this->domain) {
            case AF_INET:
                $result = socket_connect($this->fp, gethostbyname($this->host), $this->port);
                break;

            case AF_UNIX:
                $result = socket_connect($this->fp, $this->host);
                break;

            default:
                throw new ConnectionException('Connect to ' . $this->host . ':' .$this->port . ' failed: Illegal domain type ' . $this->domain . ' used.');
        }

        if (false === $result) {
            $errorMessage = $this->lastError();
            $this->fp     = null;
            throw new ConnectionException('Connect to ' . $this->host . ':' .$this->port . ' failed: ' . $errorMessage);
        }

        $this->eof = false;
        return true;
    }

    /**
     * closes a connection
     *
     * @return  BsdSocket
     */
    public function disconnect()
    {
        if ($this->isConnected() === true) {
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
     * @throws  IllegalStateException
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
     * @throws  IllegalStateException
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
     * @throws  ConnectionException
     * @throws  IllegalStateException
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
     * @throws  ConnectionException
     */
    protected function doRead($length, $type)
    {
        $result = socket_read($this->fp, $length, $type);
        if (false === $result) {
            throw new ConnectionException('Read failed: ' . $this->lastError());
        }

        if (empty($result) === true) {
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
?>