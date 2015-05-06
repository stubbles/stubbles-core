<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
use stubbles\lang;
use stubbles\peer\HeaderList;
use stubbles\peer\MalformedUriException;
use stubbles\peer\ParsedUri;
use stubbles\peer\Socket;
use stubbles\peer\Uri;
/**
 * Class for URIs of scheme hypertext transfer protocol.
 *
 * @api
 */
abstract class HttpUri extends Uri
{
    /**
     * creates http uri from given uri parts
     *
     * @param   string  $scheme       scheme of http uri, must be either http or https
     * @param   string  $host         host name of http uri
     * @param   int     $port         optional  port of http uri
     * @param   string  $path         optional  path of http uri, defaults to /
     * @param   string  $queryString  optional  query string of http uri
     * @return  \stubbles\peer\http\HttpUri
     * @since   4.0.0
     */
    public static function fromParts($scheme, $host, $port = null, $path = '/', $queryString = null)
    {
        return self::fromString(
                $scheme
                . '://'
                . $host
                . (null === $port ? '' : ':' . $port)
                . $path
                . ((null !== $queryString) ? (substr($queryString, 0, 1) !== '?' ? '?' . $queryString : $queryString) : $queryString)
        );
    }

    /**
     * parses an uri out of a string
     *
     * @param   string  $uriString  string to create instance from
     * @param   string  $rfc        optional  RFC to base validation on, defaults to Http::RFC_7230
     * @return  \stubbles\peer\http\HttpUri
     * @throws  \stubbles\lang\exception\MalformedUriException
     */
    public static function fromString($uriString, $rfc = Http::RFC_7230)
    {
        if (strlen($uriString) === 0) {
            return null;
        }

        $uri = new ConstructedHttpUri(new ParsedUri($uriString));
        if ($uri->isValidForRfc($rfc)) {
            if (!$uri->parsedUri->hasPath()) {
                $uri->parsedUri = $uri->parsedUri->transpose(['path' => '/']);
            }

            return $uri;
        }

        throw new MalformedUriException('The URI ' . $uriString . ' is not a valid HTTP URI');
    }

    /**
     * casts given value to an instance of HttpUri
     *
     * @param   string|\stubbles\peer\http\HttpUri  $value  value to cast to HttpUri
     * @param   string                              $name   optional  name of parameter to cast from
     * @return  \stubbles\peer\http\HttpUri
     * @throws  \InvalidArgumentException
     * @since   4.0.0
     */
    public static function castFrom($value, $name = 'Uri')
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_string($value)) {
            return self::fromString($value);
        }

        throw new \InvalidArgumentException(
                $name . ' must be a string containing a HTTP URI or an instance of '
                . get_class() . ', but was ' . lang\getType($value)
        );
    }

    /**
     * checks if uri is valid according to given rfc
     *
     * @param   string  $rfc
     * @return  bool
     * @throws  \stubbles\lang\exception\MalformedUriException
     */
    private function isValidForRfc($rfc)
    {
        if ($this->parsedUri->hasUser() && Http::RFC_7230 === $rfc) {
            throw new MalformedUriException('The URI ' . $this->parsedUri->asString() . ' is not a valid HTTP URI according to ' . Http::RFC_7230 . ': contains userinfo, but this is disallowed');
        }

        return $this->isValid();
    }

    /**
     * Checks whether URI is a correct URI.
     *
     * @return  bool
     */
    protected function isValid()
    {
        if (!parent::isValid()) {
            return false;
        }

        if (!$this->parsedUri->schemeEquals(Http::SCHEME) && !$this->parsedUri->schemeEquals(Http::SCHEME_SSL)) {
            return false;
        }

        return true;
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
          || checkdnsrr($this->parsedUri->hostname(), 'A')
          || checkdnsrr($this->parsedUri->hostname(), 'AAAA')
          || checkdnsrr($this->parsedUri->hostname(), 'CNAME')) {
            return true;
        }

        return false;
    }
    /**
     * checks whether the uri uses a default port or not
     *
     * Default ports are 80 for http and 443 for https
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        if (!$this->parsedUri->hasPort()) {
            return true;
        }

        if ($this->isHttp() && $this->parsedUri->portEquals(Http::PORT)) {
            return true;
        }

        if ($this->isHttps() && $this->parsedUri->portEquals(Http::PORT_SSL)) {
            return true;
        }

        return false;
    }

    /**
     * returns port of the uri
     *
     * @param   int  $defaultPort  parameter is ignored for http uris
     * @return  int
     */
    public function port($defaultPort = null)
    {

        if ($this->isHttp()) {
            return parent::port(Http::PORT);
        }

        return parent::port(Http::PORT_SSL);
    }

    /**
     * returns a new http uri instance with new path
     *
     * @param   string  $path  new path
     * @return  \stubbles\peer\http\HttpUri
     * @since   5.5.0
     */
    public function withPath($path)
    {
        return new ConstructedHttpUri(
                $this->parsedUri->transpose(['path' => $path])
        );
    }

    /**
     * checks whether current scheme is http
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttp()
    {
        return $this->parsedUri->schemeEquals(Http::SCHEME);
    }

    /**
     * checks whether current scheme is https
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttps()
    {
        return $this->parsedUri->schemeEquals(Http::SCHEME_SSL);
    }

    /**
     * transposes uri to http
     *
     * @param   int  $port  optional  new port to use, defaults to 80
     * @return  \stubbles\peer\http\HttpUri
     * @since   2.0.0
     */
    public function toHttp($port = null)
    {
        if ($this->isHttp()) {
            if ($this->parsedUri->hasPort() && null !== $port) {
                return new ConstructedHttpUri($this->parsedUri->transpose(['port' => $port]));
            }

            return $this;
        }

        $changes = ['scheme' => Http::SCHEME];
        if ($this->parsedUri->hasPort()) {
            $changes['port'] = $port;
        }

        return new ConstructedHttpUri($this->parsedUri->transpose($changes));
    }

    /**
     * transposes uri to https
     *
     * @param   int  $port  optional  new port to use, defaults to 443
     * @return  \stubbles\peer\http\HttpUri
     * @since   2.0.0
     */
    public function toHttps($port = null)
    {
        if ($this->isHttps()) {
            if ($this->parsedUri->hasPort() && null !== $port) {
                return new ConstructedHttpUri($this->parsedUri->transpose(['port' => $port]));
            }

            return $this;
        }

        $changes = ['scheme' => Http::SCHEME_SSL];
        if ($this->parsedUri->hasPort()) {
            $changes['port'] = $port;
        }

        return new ConstructedHttpUri($this->parsedUri->transpose($changes));
    }

    /**
     * creates a http connectoon for this uri
     *
     * To submit a complete HTTP request use this:
     * <code>
     * $response = $uri->connect()->asUserAgent('Not Mozilla')
     *                            ->timeout(5)
     *                            ->usingHeader('X-Money', 'Euro')
     *                            ->get();
     * </code>
     *
     * @param   \stubbles\peer\HeaderList  $headers  list of headers to be used
     * @return  \stubbles\peer\http\HttpConnection
     */
    public function connect(HeaderList $headers = null)
    {
        return new HttpConnection($this, $headers);
    }

    /**
     * creates a socket to this uri
     *
     * @return  \stubbles\peer\Socket
     * @since   6.0.0
     */
    public function createSocket()
    {
        return new Socket(
                $this->hostname(),
                $this->port(),
                (($this->isHttps()) ? ('ssl://') : (null))
        );
    }

    /**
     * opens socket to this uri
     *
     * @param   int  $timeout  connection timeout
     * @return  \stubbles\peer\Stream
     * @since   2.0.0
     */
    public function openSocket($timeout = 5)
    {
        return $this->createSocket()->connect()->setTimeout($timeout);
    }
}
