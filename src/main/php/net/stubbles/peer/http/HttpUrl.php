<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer\http;
use net\stubbles\peer\HeaderList;
use net\stubbles\peer\ParsedUrl;
use net\stubbles\peer\Socket;
use net\stubbles\peer\Url;
/**
 * Class for URLs of scheme hypertext transfer protocol.
 */
class HttpUrl extends Url implements HttpUrlContainer
{
    /**
     * constructor
     *
     * @param   ParsedUrl  $url
     */
    protected function __construct(ParsedUrl $url)
    {
        parent::__construct($url);
        if ($this->parsedUrl->getPath() == null) {
            $this->parsedUrl = $this->parsedUrl->transpose(array('path' => '/'));
        }
    }

    /**
     * Checks whether URL is a correct URL.
     *
     * @return  bool
     */
    protected function isValid()
    {
        if (!parent::isValid()) {
            return false;
        }


        if ($this->parsedUrl->getScheme() !== Http::SCHEME && $this->parsedUrl->getScheme() !== Http::SCHEME_SSL) {
            return false;
        }

        return true;
    }

    /**
     * checks whether the url uses a default port or not
     *
     * Default ports are 80 for http and 443 for https
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        if (!$this->parsedUrl->hasPort()) {
            return true;
        }

        if ($this->isHttp() && $this->parsedUrl->getPort() === Http::PORT) {
            return true;
        }

        if ($this->isHttps() && $this->parsedUrl->getPort() === Http::PORT_SSL) {
            return true;
        }

        return false;
    }

    /**
     * returns port of the url
     *
     * @param   int  $defaultPort  parameter is ignored for http urls
     * @return  int
     */
    public function getPort($defaultPort = null)
    {

        if ($this->isHttp()) {
            return parent::getPort(Http::PORT);
        }

        return parent::getPort(Http::PORT_SSL);
    }

    /**
     * checks whether current scheme is http
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttp()
    {
        return $this->parsedUrl->getScheme() === Http::SCHEME;
    }

    /**
     * checks whether current scheme is https
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttps()
    {
        return $this->parsedUrl->getScheme() === Http::SCHEME_SSL;
    }

    /**
     * transposes url to http
     *
     * @return  HttpUrl
     * @since   2.0.0
     */
    public function toHttp()
    {
        if ($this->isHttp()) {
            return $this;
        }

        return new self($this->parsedUrl->transpose(array('scheme' => Http::SCHEME)));
    }

    /**
     * transposes url to https
     *
     * @return  HttpUrl
     * @since   2.0.0
     */
    public function toHttps()
    {
        if ($this->isHttps()) {
            return $this;
        }

        return new self($this->parsedUrl->transpose(array('scheme' => Http::SCHEME_SSL)));
    }

    /**
     * creates a http connectoon for this url
     *
     * To submit a complete HTTP request use this:
     * <code>
     * $response = $url->connect()->asUserAgent('Not Mozilla')
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
     * opens socket to this url
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
?>