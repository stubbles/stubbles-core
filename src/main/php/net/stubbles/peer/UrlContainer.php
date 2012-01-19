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
use net\stubbles\lang\Object;
/**
 * Interface for URLs.
 */
interface UrlContainer extends Object
{
    /**
     * checks whether host of url is listed in dns
     *
     * @return  bool
     */
    public function hasDnsRecord();

    /**
     * returns the url as string as originally given
     *
     * @return  string
     */
    public function asString();

    /**
     * Returns url as string but without port even if originally given.
     *
     * @return  string
     */
    public function asStringWithoutPort();

    /**
     * Returns url as string containing the port if it is not the default port.
     *
     * @return  string
     */
    public function asStringWithNonDefaultPort();

    /**
     * returns the scheme of the url
     *
     * @return  string
     */
    public function getScheme();

    /**
     * returns the user
     *
     * @param   string  $defaultUser  user to return if no user is set
     * @return  string
     */
    public function getUser($defaultUser = null);
    /**
     * returns the password
     *
     * @param   string  $defaultPassword  password to return if no password is set
     * @return  string
     */
    public function getPassword($defaultPassword = null);

    /**
     * returns hostname of the url
     *
     * @return  string
     */
    public function getHost();

    /**
     * checks whether the url uses a default port or not
     *
     * @return  bool
     */
    public function hasDefaultPort();

    /**
     * returns port of the url
     *
     * @param   int  $defaultPort  port to be used if no port is defined
     * @return  int
     */
    public function getPort($defaultPort = null);

    /**
     * returns path of the url
     *
     * @return  string
     */
    public function getPath();

    /**
     * checks whether url has a query
     *
     * @return  bool
     */
    public function hasQueryString();

    /**
     * add a parameter to the url
     *
     * @param   string  $name   name of parameter
     * @param   mixed   $value  value of parameter
     * @return  UrlContainer
     */
    public function addParam($name, $value);

    /**
     * remove a param from url
     *
     * @param   string  $name  name of parameter
     * @return  UrlContainer
     * @since   1.1.2
     */
    public function removeParam($name);

    /**
     * checks whether a certain param is set
     *
     * @param   string  $name
     * @return  bool
     * @since   1.1.2
     */
    public function hasParam($name);

    /**
     * returns the value of a param
     *
     * @param   string  $name          name of the param
     * @param   mixed   $defaultValue  default value to return if param is not set
     * @return  mixed
     */
    public function getParam($name, $defaultValue = null);
}
?>