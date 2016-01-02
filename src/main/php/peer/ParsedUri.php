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
 * Represents a parses uri.
 *
 * @internal
 */
class ParsedUri
{
    /**
     * internal representation after parse_url()
     *
     * @type  array
     */
    private $uri         = [];
    /**
     * query string of uri
     *
     * @type  \stubbles\peer\QueryString
     */
    private $queryString;

    /**
     * constructor
     *
     * Passing a query string will omit any query string already present in $uri.
     *
     * @param   string                      $uri          uri to parse
     * @param   \stubbles\peer\QueryString  $queryString  optional  parameters when not in uri
     * @throws  \stubbles\peer\MalformedUriException
     */
    public function __construct($uri, QueryString $queryString = null)
    {
        $this->uri = ((!is_array($uri)) ? (parse_url($uri)): ($uri));
        if (!is_array($this->uri)) {
            throw new MalformedUriException('The URI ' . $uri . ' is not a valid URI');
        }

        if (isset($this->uri['host'])) {
            $this->uri['host'] = strtolower($this->uri['host']);
        }

        if (null !== $queryString) {
            $this->queryString = $queryString;
        } else {
            try {
                $this->queryString = new QueryString((isset($this->uri['query'])) ? ($this->uri['query']) : (null));
            } catch (\InvalidArgumentException $iae) {
                throw new MalformedUriException($iae->getMessage(), $iae);
            }
        }

        // bugfix for a PHP issue: ftp://user:@auxiliary.kl-s.com/
        // will lead to an unset $this->uri['pass'] which is wrong
        // due to RFC1738 3.1, it has to be an empty string
        if (isset($this->uri['user']) && !isset($this->uri['pass']) && $this->asString() !== $uri) {
            $this->uri['pass'] = '';
        }
    }

    /**
     * transposes the uri to another one
     *
     * This will create a new instance, leaving the existing instance unchanged.
     * The given array should contain the parts to change, where the key denotes
     * the part to change and the value the value to change to.
     *
     * The return value is a new instance with the named parts changed to the
     * new values.
     *
     * @param   array  $changedUri
     * @return  \stubbles\peer\ParsedUri
     */
    public function transpose(array $changedUri)
    {
        return new self(array_merge($this->uri, $changedUri), $this->queryString);
    }

    /**
     * returns original uri
     *
     * @return  string
     */
    public function asString()
    {
        return $this->createString(function(ParsedUri $uri) { return $uri->port();});
    }

    /**
     * returns original uri
     *
     * @return  string
     */
    public function asStringWithoutPort()
    {
        return $this->createString(function(ParsedUri $uri) { return null;});
    }

    /**
     * creates string representation of uri
     *
     * @param   \Closure  $portCreator
     * @return  string
     */
    protected function createString(\Closure $portCreator)
    {
        $uri = $this->scheme() . '://';
        if ($this->hasUser()) {
            $user = $this->user();
            if ($this->hasPassword()) {
                $user .= ':' . $this->password();
            }

            $uri .= $user;
            if ($this->hasHostname()) {
                $uri .= '@';
            }
        }

        if ($this->hasHostname()) {
            $uri .= $this->hostname();
            $port = $portCreator($this);
            if (strlen($port) > 0) {
                $uri .= ':' . $port;
            }
        }

        $uri .= $this->path();
        if ($this->queryString->hasParams()) {
            $uri .= '?' . $this->queryString->build();
        }

        if ($this->hasFragment()) {
            $uri .= '#' . $this->fragment();
        }

        return $uri;
    }

    /**
     * checks whether scheme is set
     *
     * @return  string
     */
    public function hasScheme()
    {
        return isset($this->uri['scheme']);
    }

    /**
     * checks if uri scheme equals given scheme
     *
     * @param   string  $scheme
     * @return  bool
     * @since   4.0.0
     */
    public function schemeEquals($scheme)
    {
        return $scheme === $this->scheme();
    }

    /**
     * returns the scheme of the uri
     *
     * @return  string
     */
    public function scheme()
    {
        if (isset($this->uri['scheme'])) {
            return $this->uri['scheme'];
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
        return isset($this->uri['user']);
    }

    /**
     * returns the user of the uri
     *
     * @param   string  $defaultUser  user to return if no user is set
     * @return  string
     */
    public function user($defaultUser = null)
    {
        if (isset($this->uri['user'])) {
            return $this->uri['user'];
        }

        return $defaultUser;
    }

    /**
     * checks whether password is set
     *
     * @return  string
     */
    public function hasPassword()
    {
        return isset($this->uri['pass']);
    }

    /**
     * returns the password of the uri
     *
     * @return  string
     */
    public function password()
    {
        if (isset($this->uri['pass'])) {
            return $this->uri['pass'];
        }

        return null;
    }

    /**
     * checks whether host is set
     *
     * @return  bool
     */
    public function hasHostname()
    {
        return isset($this->uri['host']);
    }

    /**
     * checks if host is local
     *
     * @return  bool
     */
    public function isLocalHost()
    {
        return in_array($this->uri['host'], ['localhost', '127.0.0.1', '[::1]']);
    }

    /**
     * returns hostname of the uri
     *
     * @return  string
     */
    public function hostname()
    {
        if (isset($this->uri['host'])) {
            return $this->uri['host'];
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
        return isset($this->uri['port']);
    }

    /**
     * checks if given port equals the uri's port
     *
     * @param   int  $port
     * @return  bool
     * @since   4.0.0
     */
    public function portEquals($port)
    {
        return $port === $this->port();
    }

    /**
     * returns port of the uri
     *
     * @return  string
     */
    public function port()
    {
        if (isset($this->uri['port'])) {
            return (int) $this->uri['port'];
        }

        return null;
    }

    /**
     * checks if path is set
     *
     * @return  bool
     * @since   4.0.0
     */
    public function hasPath()
    {
        return isset($this->uri['path']);
    }

    /**
     * returns path of the uri
     *
     * @return  string
     */
    public function path()
    {
        if (isset($this->uri['path'])) {
            return $this->uri['path'];
        }

        return null;
    }

    /**
     * returns the query string
     *
     * @return  \stubbles\peer\QueryString
     */
    public function queryString()
    {
        return $this->queryString;
    }

    /**
     * checks whether fragment is set
     *
     * @return  bool
     */
    public function hasFragment()
    {
        return isset($this->uri['fragment']);
    }

    /**
     * returns port of the uri
     *
     * @return  string
     */
    public function fragment()
    {
        if (isset($this->uri['fragment'])) {
            return $this->uri['fragment'];
        }

        return null;
    }

    /**
     * returns a string representation of the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return $this->asString();
    }
}
