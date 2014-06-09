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
use stubbles\peer\HeaderList;
/**
 * Class for connections to URIs of HTTP/HTTPS.
 */
class HttpConnection
{
    /**
     * request object to open connection
     *
     * @type  HttpUri
     */
    private $httpUri  = null;
    /**
     * contains request headers
     *
     * @type  HeaderList
     */
    private $headers  = null;
    /**
     * timeout
     *
     * @type  int
     */
    private $timeout  = 30;

    /**
     * constructor
     *
     * @param  HttpUri     $httpUri  uri to create connection to
     * @param  HeaderList  $headers  list of headers to be used
     */
    public function __construct(HttpUri $httpUri, HeaderList $headers = null)
    {
        $this->httpUri = $httpUri;
        $this->headers = ((null === $headers) ? (new HeaderList()) : ($headers));
    }

    /**
     * set timeout for connection
     *
     * @api
     * @param   int  $timeout  timeout for connection in seconds
     * @return  HttpConnection
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * do the request with the given user agent header
     *
     * @api
     * @param   string  $userAgent
     * @return  HttpConnection
     */
    public function asUserAgent($userAgent)
    {
        $this->headers->putUserAgent($userAgent);
        return $this;
    }

    /**
     * say the connection was refered from given uri
     *
     * @api
     * @param   string  $referer
     * @return  HttpConnection
     */
    public function referedFrom($referer)
    {
        $this->headers->putReferer($referer);
        return $this;
    }

    /**
     * add some cookie data to the request
     *
     * @api
     * @param   array  $cookieValues  list of key-value pairs
     * @return  HttpConnection
     */
    public function withCookie(array $cookieValues)
    {
        $this->headers->putCookie($cookieValues);
        return $this;
    }

    /**
     * authorize with given credentials
     *
     * @api
     * @param   string  $user
     * @param   string  $password
     * @return  HttpConnection
     */
    public function authorizedAs($user, $password)
    {
        $this->headers->putAuthorization($user, $password);
        return $this;
    }

    /**
     * adds any arbitrary header
     *
     * @api
     * @param   string  $key    name of header
     * @param   string  $value  value of header
     * @return  HttpConnection
     */
    public function usingHeader($key, $value)
    {
        $this->headers->put($key, $value);
        return $this;
    }

    /**
     * returns response object for given URI after GET request
     *
     * @api
     * @param   string  $version  optional  http version, defaults to HTTP/1.1
     * @return  HttpResponse
     */
    public function get($version = HttpVersion::HTTP_1_1)
    {
        return HttpRequest::create($this->httpUri, $this->headers)
                          ->get($this->timeout, $version);
    }

    /**
     * returns response object for given URI after HEAD request
     *
     * @api
     * @param   string  $version  optional  http version, defaults to HTTP/1.1
     * @return  HttpResponse
     */
    public function head($version = HttpVersion::HTTP_1_1)
    {
        return HttpRequest::create($this->httpUri, $this->headers)
                          ->head($this->timeout, $version);
    }

    /**
     * returns response object for given URI after POST request
     *
     * @api
     * @param   string|array  $body
     * @param   string        $version  optional  http version, defaults to HTTP/1.1
     * @return  HttpResponse
     */
    public function post($body, $version = HttpVersion::HTTP_1_1)
    {
        return HttpRequest::create($this->httpUri, $this->headers)
                          ->post($body, $this->timeout, $version);
    }

    /**
     * returns response object for given URI after PUT request
     *
     * @api
     * @param   string  $body
     * @param   string  $version  optional  http version, defaults to HTTP/1.1
     * @return  HttpResponse
     * @since   2.0.0
     */
    public function put($body, $version = HttpVersion::HTTP_1_1)
    {
        return HttpRequest::create($this->httpUri, $this->headers)
                          ->put($body, $this->timeout, $version);
    }

    /**
     * returns response object for given URI after DELETE request
     *
     * @api
     * @param   string  $version  optional  http version, defaults to HTTP/1.1
     * @return  HttpResponse
     * @since   2.0.0
     */
    public function delete($version = HttpVersion::HTTP_1_1)
    {
        return HttpRequest::create($this->httpUri, $this->headers)
                          ->delete($this->timeout, $version);
    }
}
