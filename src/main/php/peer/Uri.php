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
 * Class for URIs and methods on URIs.
 *
 * @api
 */
abstract class Uri
{
    /**
     * internal representation after parse_url()
     *
     * @type  \stubbles\peer\ParsedUri
     */
    protected $parsedUri;

    /**
     * parses an uri out of a string
     *
     * @param   string  $uriString  string to create instance from
     * @return  \stubbles\peer\Uri
     * @throws  \stubbles\peer\MalformedUriException
     */
    public static function fromString($uriString)
    {
        if (strlen($uriString) === 0) {
            return null;
        }

        $uri = new ConstructedUri(new ParsedUri($uriString));
        if ($uri->isValid()) {
            return $uri;
        }

        throw new MalformedUriException('The URI ' . $uriString . ' is not a valid URI.');
    }

    /**
     * Checks whether URI is a correct URI.
     *
     * @return  bool
     */
    protected function isValid()
    {
        if (!$this->parsedUri->hasScheme()) {
            return false;
        }

        if (preg_match('!^([a-z][a-z0-9\+]*)://([^@]+@)?([^/?#]*)(/([^#?]*))?(.*)$!', $this->parsedUri->asString()) == 0) {
            return false;
        }

        if ($this->parsedUri->hasUser()) {
            if (preg_match('~([@:/])~', $this->parsedUri->user()) != 0) {
                return false;
            }

            if (preg_match('~([@:/])~', $this->parsedUri->password()) != 0) {
                return false;
            }
        }

        if ($this->parsedUri->hasHostname()
          && preg_match('!^([a-zA-Z0-9\.-]+|\[[^\]]+\])(:([0-9]+))?$!', $this->parsedUri->hostname()) != 0) {
            return true;
        } elseif (!$this->parsedUri->hasHostname() || strlen($this->parsedUri->hostname()) === 0) {
            return true;
        }

        return false;
    }

    /**
     * checks whether host of uri is listed in dns
     *
     * @return  bool
     */
    public function hasDnsRecord()
    {
        if (!$this->parsedUri->hasHostname()) {
            return false;
        }

        if ($this->parsedUri->isLocalHost()
          || checkdnsrr($this->parsedUri->hostname(), 'ANY')
          || checkdnsrr($this->parsedUri->hostname(), 'MX')) {
            return true;
        }

        return false;
    }

    /**
     * returns the uri as string as originally given
     *
     * @return  string
     */
    public function asString()
    {
        return $this->parsedUri->asString();
    }


    /**
     * returns a string representation of the uri
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return $this->asString();
    }

    /**
     * Returns uri as string but without port even if originally given.
     *
     * @return  string
     */
    public function asStringWithoutPort()
    {
        return $this->parsedUri->asStringWithoutPort();
    }

    /**
     * Returns uri as string containing the port if it is not the default port.
     *
     * @return  string
     */
    public function asStringWithNonDefaultPort()
    {
        if ($this->parsedUri->hasPort() && !$this->hasDefaultPort()) {
            return $this->asString();
        }

        return $this->asStringWithoutPort();
    }

    /**
     * returns the scheme of the uri
     *
     * @return  string
     */
    public function scheme()
    {
        return $this->parsedUri->scheme();
    }

    /**
     * returns the user
     *
     * @param   string  $defaultUser  user to return if no user is set
     * @return  string
     */
    public function user($defaultUser = null)
    {
        return $this->parsedUri->user($defaultUser);
    }

    /**
     * returns the password
     *
     * @param   string  $defaultPassword  password to return if no password is set
     * @return  string
     */
    public function password($defaultPassword = null)
    {
        if (!$this->parsedUri->hasUser()) {
            return null;
        }

        if ($this->parsedUri->hasPassword()) {
            return $this->parsedUri->password();
        }

        return $defaultPassword;
    }

    /**
     * returns hostname of the uri
     *
     * @return  string
     */
    public function hostname()
    {
        return $this->parsedUri->hostname();
    }

    /**
     * checks whether the uri uses a default port or not
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        return false;
    }

    /**
     * returns port of the uri
     *
     * @param   int  $defaultPort  port to be used if no port is defined
     * @return  int
     */
    public function port($defaultPort = null)
    {
        if ($this->parsedUri->hasPort()) {
            return $this->parsedUri->port();
        }

        return $defaultPort;
    }

    /**
     * returns a new uri instance with new path
     *
     * @param   string  $path  new path
     * @return  \stubbles\peer\Uri
     * @since   5.5.0
     */
    public function withPath($path)
    {
        return new ConstructedUri($this->parsedUri->transpose(['path' => $path]));
    }

    /**
     * returns path of the uri
     *
     * @return  string
     */
    public function path()
    {
        return $this->parsedUri->path();
    }

    /**
     * checks whether uri has a query
     *
     * @return  bool
     */
    public function hasQueryString()
    {
        return $this->parsedUri->queryString()->hasParams();
    }

    /**
     * returns query string
     *
     * @return  string
     * @since   2.1.2
     */
    public function queryString()
    {
        return $this->parsedUri->queryString()->build();
    }

    /**
     * adds given map of params
     *
     * @param   array  $params  map of parameters to add
     * @return  \stubbles\peer\Uri
     * @since   5.1.2
     */
    public function addParams(array $params)
    {
        foreach ($params as $name => $value) {
            $this->addParam($name, $value);
        }

        return $this;
    }

    /**
     * add a parameter to the uri
     *
     * @param   string  $name   name of parameter
     * @param   mixed   $value  value of parameter
     * @return  \stubbles\peer\Uri
     */
    public function addParam($name, $value)
    {
        $this->parsedUri->queryString()->addParam($name, $value);
        return $this;
    }

    /**
     * remove a param from uri
     *
     * @param   string  $name  name of parameter
     * @return  \stubbles\peer\Uri
     * @since   1.1.2
     */
    public function removeParam($name)
    {
        $this->parsedUri->queryString()->removeParam($name);
        return $this;
    }

    /**
     * checks whether a certain param is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.1.2
     */
    public function hasParam($name)
    {
        return $this->parsedUri->queryString()->containsParam($name);
    }

    /**
     * returns the value of a param
     *
     * @param   string  $name          name of the param
     * @param   mixed   $defaultValue  default value to return if param is not set
     * @return  mixed
     */
    public function param($name, $defaultValue = null)
    {
        return $this->parsedUri->queryString()->param($name, $defaultValue);
    }

    /**
     * returns fragment of the uri
     *
     * @return  string
     */
    public function fragment()
    {
        return $this->parsedUri->fragment();
    }
}
