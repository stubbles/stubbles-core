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
use net\stubbles\lang\Clonable;
/**
 * Class for operations on bsd-style sockets.
 *
 * @since     2.0.0
 * @internal
 */
class SocketOptions implements Clonable
{
    /**
     * list of options for the socket
     *
     * @type  array
     */
    protected $options = array();
    /**
     * connection where options are bound to
     *
     * @type  resource
     */
    protected $connection;

    /**
     * binds options to connection
     *
     * @param   resource  $connection
     * @return  SocketOptions
     */
    public function bindToConnection($connection)
    {
        $this->connection = $connection;
        foreach ($this->options as $level => $pairs) {
            foreach ($pairs as $name => $value) {
                $this->setOnConnection($level, $name, $value);
            }
        }

        return $this;
    }

    /**
     * checks if options are bound to an connection
     *
     * @return  bool
     */
    private function boundToConnection()
    {
        return (null !== $this->connection);
    }

    /**
     * sets an option
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @param   mixed  $value  option value
     * @return  SocketOptions
     */
    public function set($level, $name, $value)
    {
        if ($this->boundToConnection()) {
            $this->setOnConnection($level, $name, $value);
        }

        return $this->store($level, $name, $value);
    }

    /**
     * returns an option
     *
     * @param   int    $level    protocol level of option
     * @param   int    $name     option name
     * @param   mixed  $default  value to return if option not set
     * @return  mixed
     */
    public function get($level, $name, $default = null)
    {
        if ($this->boundToConnection()) {
            return $this->getFromConnection($level, $name, $default);
        }

        return $this->retrieve($level, $name, $default);
    }

    /**
     * stores option internally
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @param   mixed  $value  option value
     * @return  SocketOptions
     */
    protected function store($level, $name, $value)
    {
        if (!isset($this->options[$level])) {
            $this->options[$level] = array();
        }

        $this->options[$level][$name] = $value;
        return $this;
    }

    /**
     * sets an option by applying it to given connection first
     *
     * If applying the option on the connection is successful it stores the
     * option internally.
     *
     * Failing to apply the option results in a ConnectionException.
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @param   mixed  $value  option value
     * @return  SocketOptions
     * @throws  ConnectionException
     */
    protected function setOnConnection($level, $name, $value)
    {
        if (!socket_set_option($this->connection, $level, $name, $value)) {
            throw new ConnectionException('Failed to set option ' . $name . ' on level ' . $level . ' to value ' . $value);
        }
    }

    /**
     * returns an option
     *
     * @param   int    $level    protocol level of option
     * @param   int    $name     option name
     * @param   mixed  $default  value to return if option not set
     * @return  mixed
     */
    protected function retrieve($level, $name, $default = null)
    {
        if (isset($this->options[$level]) && isset($this->options[$level][$name])) {
            return $this->options[$level][$name];
        }

        return $default;
    }

    /**
     * gets an option by retrieving it from an connection
     *
     * If the connection returns a value it is stored internally before returing
     * it.
     *
     * Failing to retrieve the option results in a ConnectionException.
     *
     * @param   int    $level    protocol level of option
     * @param   int    $name     option name
     * @param   mixed  $default  value to return if option not set
     * @return  mixed
     * @throws  ConnectionException
     */
    protected function getFromConnection($level, $name, $default)
    {
        $option = socket_get_option($this->connection, $level, $name);
        if (false === $option) {
            throw new ConnectionException('Failed to retrieve option ' . $name . ' on level ' . $level);
        }

        return $this->store($level, $name, $option)
                    ->get($level, $name, $default);
    }
}
?>