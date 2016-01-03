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
use stubbles\peer\Stream;
/**
 * Class for reading a HTTP response.
 */
class HttpResponse
{
    /**
     * the socket we read the response from
     *
     * @type  \stubbles\peer\Stream
     */
    protected $socket;
    /**
     * status line of response
     *
     * @type  string
     */
    protected $statusLine   = null;
    /**
     * http version of response
     *
     * @type  \stubbles\peer\http\HttpVersion
     */
    protected $version      = null;
    /**
     * status code of response
     *
     * @type  int
     */
    protected $statusCode   = null;
    /**
     * status code reason phrase of response
     *
     * @type  string
     */
    protected $reasonPhrase = null;
    /**
     * contains headers of response
     *
     * @type  \stubbles\peer\HeaderList
     */
    protected $headers;
    /**
     * contains body of response
     *
     * @type  string
     */
    protected $body         = null;

    /**
     * constructor
     *
     * @param  \stubbles\peer\Stream  $socket  stream to read response from
     */
    public function __construct(Stream $socket)
    {
        $this->socket  = $socket;
        $this->headers = new HeaderList();
    }

    /**
     * static constructor
     *
     * @param   \stubbles\peer\Stream  $socket  stream to read response from
     * @return  \stubbles\peer\http\HttpResponse
     * @since   2.0.0
     */
    public static function create(Stream $socket)
    {
        return new self($socket);
    }

    /**
     * returns status line of response
     *
     * @api
     * @return  string
     * @since   4.0.0
     */
    public function statusLine()
    {
        return $this->readHeader()->statusLine;
    }

    /**
     * returns http version of response
     *
     * @api
     * @return  \stubbles\peer\http\HttpVersion
     * @since   4.0.0
     */
    public function httpVersion()
    {
        return $this->readHeader()->version;
    }

    /**
     * returns status code of response
     *
     * @api
     * @return  int
     * @since   4.0.0
     */
    public function statusCode()
    {
        return $this->readHeader()->statusCode;
    }

    /**
     * return status code class of response
     *
     * @api
     * @return  string
     * @since   4.0.0
     */
    public function statusCodeClass()
    {
        $this->readHeader();
        if (empty($this->statusCode)) {
            return null;
        }

        return Http::statusClassFor($this->statusCode);
    }

    /**
     * returns reason phrase of response
     *
     * @api
     * @return  string
     * @since   5.0.0
     */
    public function reasonPhrase()
    {
        return $this->readHeader()->reasonPhrase;
    }

    /**
     * returns list of headers from response
     *
     * @api
     * @return  \stubbles\peer\HeaderList
     */
    public function headers()
    {
        return $this->readHeader()->headers;
    }

    /**
     * returns body of response
     *
     * @api
     * @return  string
     */
    public function body()
    {
        return $this->readHeader()->readBody()->body;
    }

    /**
     * reads response headers
     *
     * @return  \stubbles\peer\http\HttpResponse
     */
    private function readHeader()
    {
        if (null !== $this->statusLine) {
            return $this;
        }

        do {
            $this->parseStatusLine($this->socket->readLine());
            $headers = '';
            $line    = '';
            while (!$this->socket->eof() && Http::END_OF_LINE !== $line) {
                $line     = $this->socket->readLine() . Http::END_OF_LINE;
                $headers .= $line;
            }

            $this->headers->append($headers);
        } while ($this->requireContinue());
        return $this;
    }

    /**
     * parses first line of response
     *
     * @param  string  $statusLine  first line of response
     */
    private function parseStatusLine($statusLine)
    {
        $matches = [];
        if (preg_match("=^(HTTP/\d+\.\d+) (\d{3}) ([^\r]*)=",
                       $statusLine,
                       $matches) == false) {
            return;
        }

        $this->statusLine   = $matches[0];
        $this->version      = HttpVersion::fromString($matches[1]);
        $this->statusCode   = (int) $matches[2];
        $this->reasonPhrase = $matches[3];
    }

    /**
     * checks whether server only returned a status code which signals there's more to come
     *
     * @return  bool
     */
    private function requireContinue()
    {
        return 100 === $this->statusCode || 102 === $this->statusCode;
    }

    /**
     * reads the response body
     *
     * @return  \stubbles\peer\http\HttpResponse
     */
    private function readBody()
    {
        if (null !== $this->body) {
            return $this;
        }

        if ($this->headers->get('Transfer-Encoding') === 'chunked') {
            $this->body = $this->readChunked();
        } else {
            $this->body = $this->readDefault($this->headers->get('Content-Length', 4096));
        }

        return $this;
    }

    /**
     * helper method to read chunked response body
     *
     * The method implements the pseudo code given in RFC 2616 section 19.4.6:
     * Introduction of Transfer-Encoding. Chunk extensions are ignored.
     *
     * @return  string
     */
    private function readChunked()
    {
        $readLength = 0;
        $chunksize  = null;
        $extension  = null;
        $body       = '';
        sscanf($this->socket->readLine(), '%x%s', $chunksize, $extension);
        while (0 < $chunksize) {
            $data        = $this->socket->read($chunksize + 4);
            $body       .= rtrim($data);
            $readLength += $chunksize;
            sscanf($this->socket->readLine(), '%x', $chunksize);
        }

        $this->headers->put('Content-Length', $readLength);
        $this->headers->remove('Transfer-Encoding');
        return $body;
    }

    /**
     * helper method for default reading of response body
     *
     * @param   int     $readLength  expected length of response body
     * @return  string
     */
    private function readDefault($readLength)
    {
        $body = $buffer = '';
        $read = 0;
        while ($read < $readLength && !$this->socket->eof()) {
            $buffer  = $this->socket->read($readLength);
            $read   += strlen($buffer);
            $body   .= $buffer;
        }

        return $body;
    }
}
