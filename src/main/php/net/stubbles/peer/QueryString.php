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
/**
 * Query string handling.
 */
class QueryString extends BaseObject
{
    /**
     * parameters for url
     *
     * @type  array
     */
    protected $parameters = array();

    /**
     * constructor
     *
     * Does not use parse_str() as this breaks param names containing dots or
     * spaces.
     *
     * @param   string  $queryString
     * @throws  IllegalArgumentException
     */
    public function __construct($queryString = null)
    {
        if (empty($queryString) === false) {
            foreach (explode('&', $queryString) as $param) {
                $name = $value = null;
                sscanf($param, "%[^=]=%[^\r]", $name, $value);
                if (null === $value && substr($param, -1) == '=') {
                    $value = '';
                }

                $name = urldecode($name);
                if (substr_count($name, '[') !== substr_count($name, ']')) {
                    throw new IllegalArgumentException('Unbalanced [] in query string');
                }

                if ($start = strpos($name, '[')) {
                  $base = substr($name, 0, $start);
                  if (isset($this->parameters[$base]) === false) {
                      $this->parameters[$base] = array();
                  }

                  $ptr    = &$this->parameters[$base];
                  $offset = 0;
                  do {
                    $end = strpos($name, ']', $offset);
                    if ($start === $end - 1) {
                      $ptr = &$ptr[];
                    } else {
                      $end += substr_count($name, '[', $start + 1, $end - $start - 1);
                      $ptr  = &$ptr[substr($name, $start + 1, $end - $start - 1)];
                    }

                    $offset = $end + 1;
                  } while ($start = strpos($name, '[', $offset));

                  $ptr = urldecode($value);
                } elseif (null !== $value) {
                    $this->parameters[$name] = urldecode($value);
                } else {
                    $this->parameters[$name] = null;
                }
            }
        }
    }

    /**
     * build the query from parameters
     *
     * @return  string
     */
    public function build()
    {
        $queryString = '';
        foreach ($this->parameters as $name => $value) {
            $queryString .= $this->buildQuery($name, $value);
        }

        return substr($queryString, 1);
    }

    /**
     * Calculates query string
     *
     * @param   string  $name
     * @param   mixed   $value
     * @param   string  $postfix  The postfix to use for each variable (defaults to '')
     * @return  string
     */
    protected function buildQuery($name, $value, $postfix= '')
    {
        $query = '';
        if (is_array($value) === true) {
            foreach ($value as $k => $v) {
                if (is_int($k) === true) {
                    $query .= $this->buildQuery(null, $v, $postfix . $name.'[]');
                } else {
                    $query .= $this->buildQuery(null, $v, $postfix . $name . '[' . $k . ']');
                }
            }
        } elseif (null === $value) {
            $query .= '&' . urlencode($name) . $postfix;
        } elseif (false === $value) {
            $query .= '&' . urlencode($name) . $postfix . '=0';
        } elseif (true === $value) {
            $query .= '&' . urlencode($name) . $postfix . '=1';
        } else {
            $query .= '&' . urlencode($name) . $postfix . '=' . urlencode($value);
        }

        return $query;
    }

    /**
     * checks whether query string contains any parameters
     *
     * @return  bool
     */
    public function hasParams()
    {
        return (count($this->parameters) > 0);
    }

    /**
     * add a parameter
     *
     * @param   string  $name   name of parameter
     * @param   mixed   $value  value of parameter
     * @return  QueryString
     * @throws  IllegalArgumentException
     */
    public function addParam($name, $value)
    {
        if (is_array($value) === false && is_scalar($value) === false && null !== $value) {
            throw new IllegalArgumentException('Argument 2 passed to ' . __METHOD__ . '() must be an instance of string, array or any other scalar value.');
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * remove a param
     *
     * @param   string  $name  name of parameter
     * @return  QueryString
     */
    public function removeParam($name)
    {
        if (array_key_exists($name, $this->parameters) === true) {
            unset($this->parameters[$name]);
        }

        return $this;
    }

    /**
     * checks whether a certain param is set
     *
     * @param   string  $name
     * @return  bool
     */
    public function containsParam($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * returns the value of a param
     *
     * @param   string  $name          name of the param
     * @param   mixed   $defaultValue  default value to return if param is not set
     * @return  mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        if (array_key_exists($name, $this->parameters) === true) {
            return $this->parameters[$name];
        }

        return $defaultValue;
    }
}
?>