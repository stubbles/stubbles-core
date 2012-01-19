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
 * Container for list of headers.
 */
class HeaderList extends BaseObject implements \IteratorAggregate
{
    /**
     * list of headers
     *
     * @type  array
     */
    private $headers = array();

    /**
     * constructor
     *
     * @param  array  $headers
     * @since  2.0.0
     */
    public function __construct(array $headers = array())
    {
        $this->headers = $headers;
    }

    /**
     * creates headerlist from given string
     *
     * @param   string  $headers  string to parse for headers
     * @return  HeaderList
     */
    public static function fromString($headers)
    {
        return new self(self::parse($headers));
    }

    /**
     * parses given header string and returns a list of headers
     *
     * @param   string  $headers
     * @return  array
     */
    private static function parse($headers)
    {
        $header  = array();
        $matches = array();
        preg_match_all('=^(.+): ([^\r\n]*)=m', $headers, $matches, PREG_SET_ORDER);
        foreach ($matches as $line) {
            $header[$line[1]] = $line[2];
        }

        return $header;
    }

    /**
     * appends given headers
     *
     * If the header to append contain an already set header the existing header
     * value will be overwritten by the new one.
     *
     * @param   string|array|HeaderList  $headers
     * @return  HeaderList
     * @throws  IllegalArgumentException
     * @since   2.0.0
     */
    public function append($headers)
    {
        if (is_string($headers) === true) {
            $append = self::parse($headers);
        } elseif (is_array($headers) === true) {
            $append = $headers;
        } elseif ($headers instanceof self) {
            $append = $headers->headers;
        } else {
            throw new IllegalArgumentException('Given headers must be a string, a list of headers or another instance of ' . __CLASS__);
        }

        $this->headers = array_merge($this->headers, $append);
        return $this;
    }

    /**
     * creates header with value for key
     *
     * @param   string  $key    name of header
     * @param   string  $value  value of header
     * @return  HeaderList
     * @throws  IllegalArgumentException
     */
    public function put($key, $value)
    {
        if (is_string($key) === false) {
            throw new IllegalArgumentException('Argument 1 passed to ' . __METHOD__ . ' must be an instance of string.');
        }

        if (is_scalar($value) === false) {
            throw new IllegalArgumentException('Argument 2 passed to ' . __METHOD__ . ' must be an instance of a scalar value.');
        }

        $this->headers[$key] = (string) $value;
        return $this;
    }

    /**
     * removes header with given key
     *
     * @param   string  $key  name of header
     * @return  HeaderList
     */
    public function remove($key)
    {
        if (isset($this->headers[$key]) == true) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    /**
     * creates header for user agent
     *
     * @param   string  $userAgent  name of user agent
     * @return  HeaderList
     */
    public function putUserAgent($userAgent)
    {
        $this->put('User-Agent', $userAgent);
        return $this;
    }

    /**
     * creates header for referer
     *
     * @param   string  $referer  referer url
     * @return  HeaderList
     */
    public function putReferer($referer)
    {
        $this->put('Referer', $referer);
        return $this;
    }

    /**
     * creates header for cookie
     *
     * @param   array  $cookieValues  cookie values
     * @return  HeaderList
     */
    public function putCookie(array $cookieValues)
    {
        $cookieValue = '';
        foreach ($cookieValues as $key => $value) {
            $cookieValue .= $key . '=' . urlencode($value) . ';';
        }

        $this->put('Cookie', $cookieValue);
        return $this;
    }

    /**
     * creates header for authorization
     *
     * @param   string  $user      login name
     * @param   string  $password  login password
     * @return  HeaderList
     */
    public function putAuthorization($user, $password)
    {
        $this->put('Authorization', 'BASIC ' . base64_encode($user . ':' . $password));
        return $this;
    }

    /**
     * adds a date header
     *
     * @param   int  $date  timestamp to use as date, defaults to current timestamp
     * @return  HeaderList
     */
    public function putDate($date = null)
    {
        if (null === $date) {
            $date = gmdate('D, d M Y H:i:s');
        } else {
            $date = gmdate('D, d M Y H:i:s', $date);
        }

        $this->put('Date', $date . ' GMT');
        return $this;
    }

    /**
     * creates X-Binford header
     *
     * @return  HeaderList
     */
    public function enablePower()
    {
        $this->put('X-Binford', 'More power!');
        return $this;
    }

    /**
     * removes all headers
     *
     * @return  HeaderList
     */
    public function clear()
    {
        $this->headers = array();
        return $this;
    }

    /**
     * returns value of header with given key
     *
     * @param   string  $key      name of header
     * @param   mixed   $default  value to return if given header not set
     * @return  string
     */
    public function get($key, $default = null)
    {
        if ($this->containsKey($key) == true) {
            return $this->headers[$key];
        }

        return $default;
    }

    /**
     * returns true if an header with given key exists
     *
     * @param   string  $key  name of header
     * @return  bool
     */
    public function containsKey($key)
    {
        return isset($this->headers[$key]);
    }

    /**
     * returns an iterator object
     *
     * @return  \ArrayObject
     */
    public function getIterator()
    {
        return new \ArrayObject($this->headers);
    }

    /**
     * returns amount of headers
     *
     * @return  int
     */
    public function size()
    {
        return count($this->headers);
    }
}
?>