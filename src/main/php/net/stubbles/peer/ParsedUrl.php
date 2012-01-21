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
/**
 * Represents a parses url.
 */
class ParsedUrl extends BaseObject
{
    /**
     * internal representation after parse_url()
     *
     * @type  array
     */
    protected $url         = array();
    /**
     * query string of url
     *
     * @type  QueryString
     */
    protected $queryString;

    /**
     * constructor
     *
     * @param  string  $url
     */
    public function __construct($url)
    {
        $this->url = ((!is_array($url)) ? (parse_url($url)): ($url));
        if (isset($this->url['host'])) {
            $this->url['host'] = strtolower($this->url['host']);
        }

        $this->queryString = new QueryString((isset($this->url['query'])) ? ($this->url['query']) : (null));
        // bugfix for a PHP issue: ftp://user:@auxiliary.kl-s.com/
        // will lead to an unset $this->url['pass'] which is wrong
        // due to RFC1738 3.1, it has to be an empty string
        if (isset($this->url['user']) && !isset($this->url['pass']) && $this->asString() !== $url) {
            $this->url['pass'] = '';
        }
    }

    /**
     * transposes the url to another one
     *
     * This will create a new instance, leaving the existing instance unchanged.
     * The given array should contain the parts to change, where the key denotes
     * the part to change and the value the value to change to.
     *
     * The return value is a new instance with the named parts changed to the
     * new values.
     *
     * @param   array  $changedUrl
     * @return  ParsedUrl
     */
    public function transpose(array $changedUrl)
    {
        return new self(array_merge($this->url, $changedUrl));
    }

    /**
     * returns original url
     *
     * @return  string
     */
    public function asString()
    {
        return $this->createString(function(ParsedUrl $url) { return $url->getPort();});
    }

    /**
     * returns original url
     *
     * @return  string
     */
    public function asStringWithoutPort()
    {
        return $this->createString(function(ParsedUrl $url) { return null;});
    }

    /**
     * creates string representation of url
     *
     * @param   \Closure  $portCreator
     * @return  string
     */
    protected function createString(\Closure $portCreator)
    {
        $url = $this->getScheme() . '://';
        if ($this->hasUser()) {
            $user = $this->getUser();
            if ($this->hasPassword()) {
                $user .= ':' . $this->getPassword();
            }

            $url .= $user;
            if ($this->hasHost()) {
                $url .= '@';
            }
        }

        if ($this->hasHost()) {
            $url .= $this->getHost();
            $port = $portCreator($this);
            if (strlen($port) > 0) {
                $url .= ':' . $port;
            }
        }

        $url .= $this->getPath();
        if ($this->queryString->hasParams()) {
            $url .= '?' . $this->queryString->build();
        }

        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $url;
    }

    /**
     * checks whether scheme is set
     *
     * @return  string
     */
    public function hasScheme()
    {
        return isset($this->url['scheme']);
    }

    /**
     * returns the scheme of the url
     *
     * @return  string
     */
    public function getScheme()
    {
        if (isset($this->url['scheme'])) {
            return $this->url['scheme'];
        }

        return null;
    }

    /**
     * checks whether user is set
     *
     * @return  string
     */
    public function hasUser()
    {
        return isset($this->url['user']);
    }

    /**
     * returns the user of the url
     *
     * @return  string
     */
    public function getUser()
    {
        if (isset($this->url['user'])) {
            return $this->url['user'];
        }

        return null;
    }

    /**
     * checks whether password is set
     *
     * @return  string
     */
    public function hasPassword()
    {
        return isset($this->url['pass']);
    }

    /**
     * returns the password of the url
     *
     * @return  string
     */
    public function getPassword()
    {
        if (isset($this->url['pass'])) {
            return $this->url['pass'];
        }

        return null;
    }

    /**
     * checks whether host is set
     *
     * @return  bool
     */
    public function hasHost()
    {
        return isset($this->url['host']);
    }

    /**
     * checks if host is local
     *
     * @return  bool
     */
    public function isLocalHost()
    {
        return in_array($this->url['host'], array('localhost', '127.0.0.1', '[::1]'));
    }

    /**
     * returns hostname of the url
     *
     * @return  string
     */
    public function getHost()
    {
        if (isset($this->url['host'])) {
            return $this->url['host'];
        }

        return null;
    }

    /**
     * checks whether port is set
     *
     * @return  bool
     */
    public function hasPort()
    {
        return isset($this->url['port']);
    }

    /**
     * returns port of the url
     *
     * @return  string
     */
    public function getPort()
    {
        if (isset($this->url['port'])) {
            return (int) $this->url['port'];
        }

        return null;
    }

    /**
     * returns path of the url
     *
     * @return  string
     */
    public function getPath()
    {
        if (isset($this->url['path'])) {
            return $this->url['path'];
        }

        return null;
    }

    /**
     * returns the query string
     *
     * @return  QueryString
     */
    public function queryString()
    {
        return $this->queryString;
    }

    /**
     * checks whether port is set
     *
     * @return  bool
     */
    public function hasFragment()
    {
        return isset($this->url['fragment']);
    }

    /**
     * returns port of the url
     *
     * @return  string
     */
    public function getFragment()
    {
        if (isset($this->url['fragment'])) {
            return $this->url['fragment'];
        }

        return null;
    }
}
?>