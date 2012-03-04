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
 * Class for URIs and methods on URIs.
 *
 * @api
 */
abstract class Uri extends BaseObject
{
    /**
     * internal representation after parse_url()
     *
     * @type  ParsedUri
     */
    protected $parsedUri;

    /**
     * parses an uri out of a string
     *
     * @param   string  $uriString  string to create instance from
     * @return  Uri
     * @throws  MalformedUriException
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
            if (preg_match('~([@:/])~', $this->parsedUri->getUser()) != 0) {
                return false;
            }

            if (preg_match('~([@:/])~', $this->parsedUri->getPassword()) != 0) {
                return false;
            }
        }

        if ($this->parsedUri->hasHost()
          && preg_match('!^([a-zA-Z0-9\.-]+|\[[^\]]+\])(:([0-9]+))?$!', $this->parsedUri->getHost()) != 0) {
            return true;
        } elseif (!$this->parsedUri->hasHost() || strlen($this->parsedUri->getHost()) === 0) {
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
        if (!$this->parsedUri->hasHost()) {
            return false;
        }

        if ($this->parsedUri->isLocalHost()
          || checkdnsrr($this->parsedUri->getHost(), 'ANY')
          || checkdnsrr($this->parsedUri->getHost(), 'MX')) {
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
    public function getScheme()
    {
        return $this->parsedUri->getScheme();
    }

    /**
     * returns the user
     *
     * @param   string  $defaultUser  user to return if no user is set
     * @return  string
     */
    public function getUser($defaultUser = null)
    {
        return $this->parsedUri->getUser($defaultUser);
    }

    /**
     * returns the password
     *
     * @param   string  $defaultPassword  password to return if no password is set
     * @return  string
     */
    public function getPassword($defaultPassword = null)
    {
        if (!$this->parsedUri->hasUser()) {
            return null;
        }

        if ($this->parsedUri->hasPassword()) {
            return $this->parsedUri->getPassword();
        }

        return $defaultPassword;
    }

    /**
     * returns hostname of the uri
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->parsedUri->getHost();
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
    public function getPort($defaultPort = null)
    {
        if ($this->parsedUri->hasPort()) {
            return $this->parsedUri->getPort();
        }

        return $defaultPort;
    }

    /**
     * returns path of the uri
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->parsedUri->getPath();
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
     * add a parameter to the uri
     *
     * @param   string  $name   name of parameter
     * @param   mixed   $value  value of parameter
     * @return  Uri
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
     * @return  Uri
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
    public function getParam($name, $defaultValue = null)
    {
        return $this->parsedUri->queryString()->getParam($name, $defaultValue);
    }

    /**
     * returns fragment of the uri
     *
     * @return  string
     */
    public function getFragment()
    {
        return $this->parsedUri->getFragment();
    }
}
?>