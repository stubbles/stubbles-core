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
use net\stubbles\streams\InputStream;
/**
 * Class for reading a HTTP response.
 */
class HttpResponse extends BaseObject
{
    /**
     * the socket we read the response from
     *
     * @type  InputStream
     */
    protected $inputStream;
    /**
     * status line of response
     *
     * @type  string
     */
    protected $statusLine   = null;
    /**
     * http version of response
     *
     * @type  string
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
     * @type  HeaderList
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
     * @param  InputStream  $inputStream  stream to read response from
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
        $this->headers     = new HeaderList();
    }

    /**
     * static constructor
     *
     * @param   InputStream  $inputStream  stream to read response from
     * @return  HttpResponse
     * @since   2.0.0
     */
    public static function create(InputStream $inputStream)
    {
        return new self($inputStream);
    }

    /**
     * returns status line of response
     *
     * @api
     * @return  string
     * @since   2.0.0
     */
    public function getStatusLine()
    {
        return $this->readHeader()->statusLine;
    }

    /**
     * returns http version of response
     *
     * @api
     * @return  string
     * @since   2.0.0
     */
    public function getHttpVersion()
    {
        return $this->readHeader()->version;
    }

    /**
     * returns status code of response
     *
     * @api
     * @return  int
     * @since   2.0.0
     */
    public function getStatusCode()
    {
        return $this->readHeader()->statusCode;
    }

    /**
     * return status code class of response
     *
     * @api
     * @return  string
     * @since   2.0.0
     */
    public function getStatusCodeClass()
    {
        $this->readHeader();
        if (empty($this->statusCode)) {
            return null;
        }

        return Http::getStatusClass($this->statusCode);
    }

    /**
     * returns reason phrase of response
     *
     * @api
     * @return  string
     * @since   2.0.0
     */
    public function getReasonPhrase()
    {
        return $this->readHeader()->reasonPhrase;
    }

    /**
     * returns list of headers from response
     *
     * @api
     * @return  HeaderList
     */
    public function getHeader()
    {
        return $this->readHeader()->headers;
    }

    /**
     * returns body of response
     *
     * @api
     * @return  string
     */
    public function getBody()
    {
        return $this->readHeader()->readBody()->body;
    }

    /**
     * reads response headers
     *
     * @return  HttpResponse
     */
    private function readHeader()
    {
        if (null !== $this->statusLine) {
            return $this;
        }

        do {
            $this->parseStatusLine($this->inputStream->readLine());
            $headers = '';
            $line    = '';
            while (!$this->inputStream->eof() && Http::END_OF_LINE !== $line) {
                $line     = $this->inputStream->readLine() . Http::END_OF_LINE;
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
        $matches = array();
        if (preg_match("=^(HTTP/\d+\.\d+) (\d{3}) ([^\r]*)=",
                       $statusLine,
                       $matches) == false) {
            return;
        }

        $this->statusLine   = $matches[0];
        $this->version      = $matches[1];
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
     * @return  HttpResponse
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
        sscanf($this->inputStream->readLine(1024), "%x%s\r", $chunksize, $extension);
        while (0 < $chunksize) {
            $data        = $this->inputStream->read($chunksize + 2);
            $body       .= rtrim($data);
            $readLength += $chunksize;
            sscanf($this->inputStream->readLine(1024), "%x\r", $chunksize);
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
        while ($read < $readLength && !$this->inputStream->eof()) {
            $buffer  = $this->inputStream->read($readLength);
            $read   += strlen($buffer);
            $body   .= $buffer;
        }

        return $body;
    }
}
?>