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
 * Class for URLs and methods on URLs.
 */
class Url extends BaseObject implements UrlContainer
{
    /**
     * internal representation after parse_url()
     *
     * @type  ParsedUrl
     */
    protected $parsedUrl;

    /**
     * constructor
     *
     * @param  ParsedUrl  $url
     */
    protected function __construct(ParsedUrl $url)
    {
        $this->parsedUrl = $url;
    }

    /**
     * parses an url out of a string
     *
     * @param   string  $urlString  string to create instance from
     * @return  Url
     * @throws  MalformedUrlException
     */
    public static function fromString($urlString)
    {
        if (strlen($urlString) === 0) {
            return null;
        }

        $class = get_called_class();
        $url   = new $class(new ParsedUrl($urlString));
        if ($url->isValid()) {
            return $url;
        }

        throw new MalformedUrlException('The url ' . $urlString . ' is not a valid url.');
    }

    /**
     * Checks whether URL is a correct URL.
     *
     * @return  bool
     */
    protected function isValid()
    {
        if (!$this->parsedUrl->hasScheme()) {
            return false;
        }

        if (preg_match('!^([a-z][a-z0-9\+]*)://([^@]+@)?([^/?#]*)(/([^#?]*))?(.*)$!', $this->parsedUrl->asString()) == 0) {
            return false;
        }

        if ($this->parsedUrl->hasUser()) {
            if (preg_match('~([@:/])~', $this->parsedUrl->getUser()) != 0) {
                return false;
            }

            if (preg_match('~([@:/])~', $this->parsedUrl->getPassword()) != 0) {
                return false;
            }
        }

        if ($this->parsedUrl->hasHost()
          && preg_match('!^([a-zA-Z0-9\.-]+|\[[^\]]+\])(:([0-9]+))?$!', $this->parsedUrl->getHost()) != 0) {
            return true;
        } elseif (!$this->parsedUrl->hasHost() || strlen($this->parsedUrl->getHost()) === 0) {
            return true;
        }

        return false;
    }

    /**
     * checks whether host of url is listed in dns
     *
     * @return  bool
     */
    public function hasDnsRecord()
    {
        if (!$this->parsedUrl->hasHost()) {
            return false;
        }

        if ($this->parsedUrl->isLocalHost()
          || checkdnsrr($this->parsedUrl->getHost(), 'ANY')
          || checkdnsrr($this->parsedUrl->getHost(), 'MX')) {
            return true;
        }

        return false;
    }

    /**
     * returns the url as string as originally given
     *
     * @return  string
     */
    public function asString()
    {
        return $this->parsedUrl->asString();
    }

    /**
     * Returns url as string but without port even if originally given.
     *
     * @return  string
     */
    public function asStringWithoutPort()
    {
        return $this->parsedUrl->asStringWithoutPort();
    }

    /**
     * Returns url as string containing the port if it is not the default port.
     *
     * @return  string
     */
    public function asStringWithNonDefaultPort()
    {
        if ($this->parsedUrl->hasPort() && !$this->hasDefaultPort()) {
            return $this->asString();
        }

        return $this->asStringWithoutPort();
    }

    /**
     * returns the scheme of the url
     *
     * @return  string
     */
    public function getScheme()
    {
        return $this->parsedUrl->getScheme();
    }

    /**
     * returns the user
     *
     * @param   string  $defaultUser  user to return if no user is set
     * @return  string
     */
    public function getUser($defaultUser = null)
    {
        return $this->parsedUrl->getUser($defaultUser);
    }

    /**
     * returns the password
     *
     * @param   string  $defaultPassword  password to return if no password is set
     * @return  string
     */
    public function getPassword($defaultPassword = null)
    {
        if (!$this->parsedUrl->hasUser()) {
            return null;
        }

        if ($this->parsedUrl->hasPassword()) {
            return $this->parsedUrl->getPassword();
        }

        return $defaultPassword;
    }

    /**
     * returns hostname of the url
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->parsedUrl->getHost();
    }

    /**
     * checks whether the url uses a default port or not
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        return false;
    }

    /**
     * returns port of the url
     *
     * @param   int  $defaultPort  port to be used if no port is defined
     * @return  int
     */
    public function getPort($defaultPort = null)
    {
        if ($this->parsedUrl->hasPort()) {
            return $this->parsedUrl->getPort();
        }

        return $defaultPort;
    }

    /**
     * returns path of the url
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->parsedUrl->getPath();
    }

    /**
     * checks whether url has a query
     *
     * @return  bool
     */
    public function hasQueryString()
    {
        return $this->parsedUrl->queryString()->hasParams();
    }

    /**
     * add a parameter to the url
     *
     * @param   string  $name   name of parameter
     * @param   mixed   $value  value of parameter
     * @return  Url
     */
    public function addParam($name, $value)
    {
        $this->parsedUrl->queryString()->addParam($name, $value);
        return $this;
    }

    /**
     * remove a param from url
     *
     * @param   string  $name  name of parameter
     * @return  Url
     * @since   1.1.2
     */
    public function removeParam($name)
    {
        $this->parsedUrl->queryString()->removeParam($name);
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
        return $this->parsedUrl->queryString()->containsParam($name);
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
        return $this->parsedUrl->queryString()->getParam($name, $defaultValue);
    }

    /**
     * returns fragment of the url
     *
     * @return  string
     */
    public function getFragment()
    {
        return $this->parsedUrl->getFragment();
    }
}
?>