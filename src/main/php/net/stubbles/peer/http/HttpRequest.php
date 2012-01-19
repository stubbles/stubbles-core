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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\peer\HeaderList;
use net\stubbles\peer\Socket;
use net\stubbles\streams\OutputStream;
/**
 * Class for sending a HTTP request.
 */
class HttpRequest extends BaseObject
{
    /**
     * the http address to setup a connection to
     *
     * @type  HttpUrlContainer
     */
    protected $httpUrl = null;
    /**
     * contains request headers
     *
     * @type  HeaderList
     */
    protected $headers = null;

    /**
     * constructor
     *
     * @param  HttpUrlContainer  $httpUrl  HTTP URL to perform a request to
     * @param  HeaderList        $header   list of request headers
     */
    public function __construct(HttpUrlContainer $httpUrl, HeaderList $header)
    {
        $this->httpUrl = $httpUrl;
        $this->headers = $header;
    }

    /**
     * static constructor
     *
     * @param   HttpUrlContainer  $httpUrl
     * @param   HeaderList        $header
     * @return  HttpRequest
     * @since   2.0.0
     */
    public static function create(HttpUrlContainer $httpUrl, HeaderList $header)
    {
        return new self($httpUrl, $header);
    }

    /**
     * initializes a get request
     *
     * @param   int     $timeout  connection timeout
     * @param   string  $version  http version
     * @return  HttpResponse
     */
    public function get($timeout = 30, $version = Http::VERSION_1_1)
    {
        $socket = $this->httpUrl->openSocket($timeout);
        $this->processHeader($socket->getOutputStream(), Http::GET, $version);
        return HttpResponse::create($socket->getInputStream());
    }

    /**
     * initializes a head request
     *
     * @param   int     $timeout  connection timeout
     * @param   string  $version  http version
     * @return  HttpResponse
     */
    public function head($timeout = 30, $version = Http::VERSION_1_1)
    {
        $socket = $this->httpUrl->openSocket($timeout);
        $this->headers->put('Connection', 'close');
        $this->processHeader($socket->getOutputStream(), Http::HEAD, $version);
        return HttpResponse::create($socket->getInputStream());
    }

    /**
     * initializes a post request
     *
     * The body can either be given as string or as array, which is considered
     * to be a map of key-value pairs denoting post request parameters. If the
     * latter is the case an post form submit content type will be added to the
     * request.
     *
     * @param   string|array  $body     post request body
     * @param   int           $timeout  connection timeout
     * @param   string        $version  http version
     * @return  HttpResponse
     */
    public function post($body, $timeout = 30, $version = Http::VERSION_1_1)
    {
        if (is_array($body) === true) {
            $body = $this->transformPostValues($body);
            $this->headers->put('Content-Type', 'application/x-www-form-urlencoded');
        }

        $this->headers->put('Content-Length', strlen($body));
        $socket = $this->httpUrl->openSocket($timeout);
        $out    = $socket->getOutputStream();
        $this->processHeader($out, Http::POST, $version);
        $out->write($body);
        return HttpResponse::create($socket->getInputStream());
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
     * @param   OutputStream  $out      output stream to write request to
     * @param   string        $method   http method
     * @param   string        $version  http version
     * @throws  IllegalArgumentException
     */
    private function processHeader(OutputStream $out, $method, $version)
    {
        if (Http::isVersionValid($version) === false) {
            throw new IllegalArgumentException("Invalid HTTP version " . $version . ', please use either ' . Http::VERSION_1_0 . ' or ' . Http::VERSION_1_1);
        }

        $out->write(Http::line($method . ' ' . $this->httpUrl->getPath() . ' ' . $version));
        $out->write(Http::line('Host: ' . $this->httpUrl->getHost()));
        foreach ($this->headers as $key => $value) {
            $out->write(Http::line($key . ': ' . $value));
        }

        $out->write(Http::emptyLine());
    }
}
?>