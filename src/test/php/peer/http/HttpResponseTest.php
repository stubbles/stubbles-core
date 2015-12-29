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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isEmpty;
use function bovigo\assert\predicate\isNull;
/**
 * Test for stubbles\peer\http\HttpResponse.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates instance to test
     *
     * @param   string  $response  content of response
     * @return  HttpResponse
     */
    private function createResponse($response)
    {
        return HttpResponse::create(new MemoryInputStream($response));
    }

    /**
     * @test
     */
    public function chunkedResponseCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'HTTP/1.1 200 OK',
                        'Host: localhost',
                        'Transfer-Encoding: chunked',
                        '',
                        dechex(3) . " ext\r\n",
                        "foo\r\n",
                        dechex(3) . "\r\n",
                        "bar\r\n",
                        dechex(0) . "\r\n"
                )
        );
        assert($httpResponse->body(), equals('foobar'));
        $headerList = $httpResponse->headers();
        assert($headerList->get('Host'), equals('localhost'));
        assert($headerList->get('Content-Length'), equals(6));

    }

    /**
     * @test
     */
    public function nonChunkedResponseWithoutContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'HTTP/1.1 200 OK',
                        'Host: localhost',
                        '',
                        'foobar'
                )
        );
        $headerList = $httpResponse->headers();
        assert($headerList->get('Host'), equals('localhost'));
        assert($httpResponse->body(), equals('foobar'));
    }

    /**
     * @test
     */
    public function nonChunkedResponseWithContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'HTTP/1.1 200 OK',
                        'Host: localhost',
                        'Content-Length: 6',
                        '',
                        'foobar'
                )
        );
        $headerList = $httpResponse->headers();
        assert($headerList->get('Host'), equals('localhost'));
        assert($headerList->get('Content-Length'), equals(6));
        assert($httpResponse->body(), equals('foobar'));
    }

    /**
     * @test
     */
    public function canReadResponseTwice()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'HTTP/1.1 200 OK',
                        'Host: localhost',
                        'Content-Length: 6',
                        '',
                        'foobar'
                )
        );
        assert($httpResponse->body(), equals('foobar'));
        assert($httpResponse->body(), equals('foobar'));
    }

    /**
     * @test
     */
    public function continuesOnStatusCode100()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 100 Continue')
                . Http::line('Host: localhost')
                . Http::emptyLine()
                . Http::line('HTTP/1.0 100 Continue')
                . Http::emptyLine()
                . Http::line('HTTP/1.0 200 OK')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        assert($headerList->get('Host'), equals('localhost'));
        assert($httpResponse->statusLine(), equals('HTTP/1.0 200 OK'));
        assert($httpResponse->httpVersion(), equals(new HttpVersion(1, 0)));
        assert($httpResponse->statusCode(), equals(200));
        assert($httpResponse->reasonPhrase(), equals('OK'));
        assert($httpResponse->statusCodeClass(), equals(Http::STATUS_CLASS_SUCCESS));
        assert($httpResponse->body(), equals('foobar'));
    }

    /**
     * @test
     */
    public function continuesOnStatusCode102()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 102 Processing')
                . Http::line('Host: localhost')
                . Http::emptyLine()
                . Http::line('HTTP/1.0 102 Processing')
                . Http::emptyLine()
                . Http::line('HTTP/1.1 404 Not Found')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        assert($headerList->get('Host'), equals('localhost'));
        assert($httpResponse->statusLine(), equals('HTTP/1.1 404 Not Found'));
        assert($httpResponse->httpVersion(), equals(new HttpVersion(1, 1)));
        assert($httpResponse->statusCode(), equals(404));
        assert($httpResponse->reasonPhrase(), equals('Not Found'));
        assert($httpResponse->statusCodeClass(), equals(Http::STATUS_CLASS_ERROR_CLIENT));
        assert($httpResponse->body(), equals('foobar'));
    }

    /**
     * @test
     */
    public function illegalStatusLineLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'Illegal Response',
                        'Host: localhost',
                        ''
                )
        );
        assert($httpResponse->statusLine(), isNull());
        assert($httpResponse->httpVersion(), isNull());
        assert($httpResponse->statusCode(), isNull());
        assert($httpResponse->reasonPhrase(), isNull());
        assert($httpResponse->statusCodeClass(), isNull());
        assert($httpResponse->body(), isEmpty());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function statusLineWithInvalidHttpVersionLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        'HTTP/400 102 Processing',
                        'Host: localhost',
                        ''
                )
        );
        assert($httpResponse->statusLine(), isNull());
        assert($httpResponse->httpVersion(), isNull());
        assert($httpResponse->statusCode(), isNull());
        assert($httpResponse->reasonPhrase(), isNull());
        assert($httpResponse->statusCodeClass(), isNull());
        assert($httpResponse->body(), isEmpty());
    }
}
