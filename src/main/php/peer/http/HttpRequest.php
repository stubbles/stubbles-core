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
use stubbles\streams\OutputStream;
/**
 * Class for sending a HTTP request.
 *
 * @internal
 */
class HttpRequest
{
    /**
     * the http address to setup a connection to
     *
     * @type  \stubbles\peer\http\HttpUri
     */
    protected $httpUri = null;
    /**
     * contains request headers
     *
     * @type  \stubbles\peer\HeaderList
     */
    protected $headers = null;

    /**
     * constructor
     *
     * @param  \stubbles\peer\http\HttpUri  $httpUri  HTTP URI to perform a request to
     * @param  \stubbles\peer\HeaderList    $header   list of request headers
     */
    public function __construct(HttpUri $httpUri, HeaderList $header)
    {
        $this->httpUri = $httpUri;
        $this->headers = $header;
    }

    /**
     * static constructor
     *
     * @param   \stubbles\peer\http\HttpUri  $httpUri
     * @param   \stubbles\peer\HeaderList    $header
     * @return  \stubbles\peer\http\HttpRequest
     * @since   2.0.0
     */
    public static function create(HttpUri $httpUri, HeaderList $header)
    {
        return new self($httpUri, $header);
    }

    /**
     * initializes a get request
     *
     * @param   int                                     $timeout  optional  connection timeout, defaults to 30 seconds
     * @param   string|\stubbles\peer\http\HttpVersion  $version  optional  http version, defaults to HTTP/1.1
     * @return  \stubbles\peer\http\HttpResponse
     */
    public function get($timeout = 30, $version = HttpVersion::HTTP_1_1)
    {
        $socket = $this->httpUri->openSocket($timeout);
        $this->processHeader($socket->out(), Http::GET, $version);
        return HttpResponse::create($socket->in());
    }

    /**
     * initializes a head request
     *
     * @param   int                                     $timeout  optional  connection timeout, defaults to 30 seconds
     * @param   string|\stubbles\peer\http\HttpVersion  $version  optional  http version, defaults to HTTP/1.1
     * @return  \stubbles\peer\http\HttpResponse
     */
    public function head($timeout = 30, $version = HttpVersion::HTTP_1_1)
    {
        $socket = $this->httpUri->openSocket($timeout);
        $this->headers->put('Connection', 'close');
        $this->processHeader($socket->out(), Http::HEAD, $version);
        return HttpResponse::create($socket->in());
    }

    /**
     * initializes a post request
     *
     * The body can either be given as string or as array, which is considered
     * to be a map of key-value pairs denoting post request parameters. If the
     * latter is the case an post form submit content type will be added to the
     * request.
     *
     * @param   string|array                            $body     post request body
     * @param   int                                     $timeout  optional  connection timeout, defaults to 30 seconds
     * @param   string|\stubbles\peer\http\HttpVersion  $version  optional  http version, defaults to HTTP/1.1
     * @return  \stubbles\peer\http\HttpResponse
     */
    public function post($body, $timeout = 30, $version = HttpVersion::HTTP_1_1)
    {
        if (is_array($body)) {
            $body = $this->transformPostValues($body);
            $this->headers->put('Content-Type', 'application/x-www-form-urlencoded');
        }

        $this->headers->put('Content-Length', strlen($body));
        $socket = $this->httpUri->openSocket($timeout);
        $out    = $socket->out();
        $this->processHeader($out, Http::POST, $version);
        $out->write($body);
        return HttpResponse::create($socket->in());
    }

    /**
     * initializes a put request
     *
     * @param   string                                  $body     post request body
     * @param   int                                     $timeout  optional  connection timeout, defaults to 30 seconds
     * @param   string|\stubbles\peer\http\HttpVersion  $version  optional  http version, defaults to HTTP/1.1
     * @return  \stubbles\peer\http\HttpResponse
     * @since   2.0.0
     */
    public function put($body, $timeout = 30, $version = HttpVersion::HTTP_1_1)
    {
        $this->headers->put('Content-Length', strlen($body));
        $socket = $this->httpUri->openSocket($timeout);
        $out    = $socket->out();
        $this->processHeader($out, Http::PUT, $version);
        $out->write($body);
        return HttpResponse::create($socket->in());
    }

    /**
     * initializes a put request
     *
     * @param   int                                     $timeout  optional  connection timeout, defaults to 30 seconds
     * @param   string|\stubbles\peer\http\HttpVersion  $version  optional  http version, defaults to HTTP/1.1
     * @return  \stubbles\peer\http\HttpResponse
     * @since   2.0.0
     */
    public function delete($timeout = 30, $version = HttpVersion::HTTP_1_1)
    {
        $socket = $this->httpUri->openSocket($timeout);
        $this->processHeader($socket->out(), Http::DELETE, $version);
        return HttpResponse::create($socket->in());
    }

    /**
     * transforms post values to post body
     *
     * @param   array  $postValues
     * @return  string
     */
    private function transformPostValues(array $postValues)
    {
        $body = '';
        foreach ($postValues as $key => $value) {
            $body .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        return $body;
    }

    /**
     * helper method to send the headers
     *
     * @param   \stubbles\streams\OutputStream          $out      output stream to write request to
     * @param   string                                  $method   http method
     * @param   string|\stubbles\peer\http\HttpVersion  $version  http version
     * @throws  \InvalidArgumentException
     */
    private function processHeader(OutputStream $out, $method, $version)
    {
        $version = HttpVersion::castFrom($version);
        if (!$version->equals(HttpVersion::HTTP_1_0) && !$version->equals(HttpVersion::HTTP_1_1)) {
            throw new \InvalidArgumentException(
                    'Invalid HTTP version ' . $version . ', please use either '
                    . HttpVersion::HTTP_1_0 . ' or ' . HttpVersion::HTTP_1_1
            );
        }

        $path = $this->httpUri->path();
        if ($this->httpUri->hasQueryString() && $this->methodAllowsQueryString($method)) {
            $path .= '?' . $this->httpUri->queryString();
        }

        $out->write(Http::line($method . ' ' . $path . ' ' . $version));
        $out->write(Http::line('Host: ' . $this->httpUri->hostname()));
        foreach ($this->headers as $key => $value) {
            $out->write(Http::line($key . ': ' . $value));
        }

        $out->write(Http::emptyLine());
    }

    /**
     * checks if given method allows a query string
     *
     * @param   string  $method
     * @return  bool
     */
    private function methodAllowsQueryString($method)
    {
        return (Http::GET === $method || Http::HEAD === $method);
    }
}
