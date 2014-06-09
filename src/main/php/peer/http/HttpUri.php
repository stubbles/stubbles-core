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
use stubbles\lang\exception\IllegalArgumentException;
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
     * parses an uri out of a string
     *
     * @param   string  $uriString  string to create instance from
     * @param   string  $rfc        optional  RFC to base validation on, defaults to Http::RFC_7230
     * @return  HttpUri
     * @throws  IllegalArgumentException  when passed RFC is unknown
     * @throws  MalformedUriException
     */
    public static function fromString($uriString, $rfc = Http::RFC_7230)
    {
        if (!Http::isValidRfc($rfc)) {
            throw new IllegalArgumentException('Unknown RFC ' . $rfc . ', please use one of ' . Http::RFC_2616 . ' or ' . Http::RFC_7230);
        }

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
     * checks if uri is valid according to given rfc
     *
     * @param   string  $rfc
     * @return  bool
     * @throws  MalformedUriException
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
     * returns port of the uri
     *
     * @param   int  $defaultPort  parameter is ignored for http uris
     * @return  int
     * @deprecated  since 4.0.0, use port() instead, will be removed with 5.0.0
     */
    public function getPort($defaultPort = null)
    {
        return $this->port($defaultPort);
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
     * @return  HttpUri
     * @since   2.0.0
     */
    public function toHttp()
    {
        if ($this->isHttp()) {
            return $this;
        }

        return new ConstructedHttpUri($this->parsedUri->transpose(['scheme' => Http::SCHEME]));
    }

    /**
     * transposes uri to https
     *
     * @return  HttpUri
     * @since   2.0.0
     */
    public function toHttps()
    {
        if ($this->isHttps()) {
            return $this;
        }

        return new ConstructedHttpUri($this->parsedUri->transpose(['scheme' => Http::SCHEME_SSL]));
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
     * @param   HeaderList  $headers  list of headers to be used
     * @return  HttpConnection
     */
    public function connect(HeaderList $headers = null)
    {
        return new HttpConnection($this, $headers);
    }

    /**
     * opens socket to this uri
     *
     * @param   int  $timeout  connection timeout
     * @return  Socket
     * @since   2.0.0
     */
    public function openSocket($timeout = 5)
    {
        return new Socket($this->getHost(),
                          $this->getPort(),
                          (($this->isHttps()) ? ('ssl://') : (null)),
                          $timeout
        );
    }
}
