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
use net\stubbles\lang\BaseObject;
use net\stubbles\peer\HeaderList;
/**
 * Class for connections to URLs of HTTP/HTTPS.
 */
class HttpConnection extends BaseObject
{
    /**
     * request object to open connection
     *
     * @type  HttpUrlContainer
     */
    protected $httpUrl  = null;
    /**
     * contains request headers
     *
     * @type  HeaderList
     */
    protected $headers  = null;
    /**
     * timeout
     *
     * @type  int
     */
    protected $timeout  = 30;

    /**
     * constructor
     *
     * @param  HttpUrlContainer  $httpUrl  url to create connection to
     * @param  HeaderList        $headers  list of headers to be used
     */
    public function __construct(HttpUrlContainer $httpUrl, HeaderList $headers = null)
    {
        $this->httpUrl = $httpUrl;
        $this->headers = ((null === $headers) ? (new HeaderList()) : ($headers));
    }

    /**
     * set timeout for connection
     *
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
     * @param   string  $userAgent
     * @return  HttpConnection
     */
    public function asUserAgent($userAgent)
    {
        $this->headers->putUserAgent($userAgent);
        return $this;
    }

    /**
     * say the connection was refered from given url
     *
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
     * returns response object for given URL after GET request
     *
     * @param   string  $version  HTTP version
     * @return  HttpResponse
     */
    public function get($version = Http::VERSION_1_1)
    {
        return HttpRequest::create($this->httpUrl, $this->headers)
                          ->get($this->timeout, $version);
    }

    /**
     * returns response object for given URL after HEAD request
     *
     * @param   string  $version  HTTP version
     * @return  HttpResponse
     */
    public function head($version = Http::VERSION_1_1)
    {
        return HttpRequest::create($this->httpUrl, $this->headers)
                          ->head($this->timeout, $version);
    }

    /**
     * returns response object for given URL after POST request
     *
     * @param   string|array  $body
     * @param   string        $version  HTTP version
     * @return  HttpResponse
     */
    public function post($body, $version = Http::VERSION_1_1)
    {
        return HttpRequest::create($this->httpUrl, $this->headers)
                          ->post($body, $this->timeout, $version);
    }
}
?>