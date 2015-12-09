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
use stubbles\streams\memory\MemoryInputStream;
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
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Transfer-Encoding: chunked',
                         '',
                         dechex(3) . " ext\r\n",
                         "foo\r\n",
                         dechex(3) . "\r\n",
                         "bar\r\n",
                         dechex(0) . "\r\n"
                        ]
                )
        );
        assertEquals('foobar', $httpResponse->body());
        $headerList = $httpResponse->headers();
        assertInstanceOf(HeaderList::class, $headerList);
        assertEquals(__METHOD__, $headerList->get('Host'));
        assertEquals(6, $headerList->get('Content-Length'));

    }

    /**
     * @test
     */
    public function nonChunkedResponseWithoutContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         '',
                         'foobar'
                        ]
                )
        );
        $headerList = $httpResponse->headers();
        assertInstanceOf(HeaderList::class, $headerList);
        assertEquals(__METHOD__, $headerList->get('Host'));
        assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function nonChunkedResponseWithContentLengthHeaderCanBeRead()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                )
        );
        $headerList = $httpResponse->headers();
        assertInstanceOf(HeaderList::class, $headerList);
        assertEquals(__METHOD__, $headerList->get('Host'));
        assertEquals(6, $headerList->get('Content-Length'));
        assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function canReadResponseTwice()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/1.1 200 OK',
                         'Host: ' . __METHOD__,
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                )
                        );
        assertEquals('foobar', $httpResponse->body());
        assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function continuesOnStatusCode100()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 100 Continue')
                . Http::line('Host: ' . __METHOD__)
                . Http::emptyLine()
                . Http::line('HTTP/1.0 100 Continue')
                . Http::emptyLine()
                . Http::line('HTTP/1.0 200 OK')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        assertInstanceOf(HeaderList::class, $headerList);
        assertEquals(__METHOD__, $headerList->get('Host'));
        assertEquals('HTTP/1.0 200 OK', $httpResponse->statusLine());
        assertEquals(new HttpVersion(1, 0), $httpResponse->httpVersion());
        assertEquals(200, $httpResponse->statusCode());
        assertEquals('OK', $httpResponse->reasonPhrase());
        assertEquals(Http::STATUS_CLASS_SUCCESS, $httpResponse->statusCodeClass());
        assertEquals('foobar', $httpResponse->body());
    }

    /**
     * @test
     */
    public function continuesOnStatusCode102()
    {
        $httpResponse = $this->createResponse(
                Http::line('HTTP/1.0 102 Processing')
                . Http::line('Host: ' . __METHOD__)
                . Http::emptyLine()
                . Http::line('HTTP/1.0 102 Processing')
                . Http::emptyLine()
                . Http::line('HTTP/1.1 404 Not Found')
                . Http::emptyLine()
                . 'foobar'
        );
        $headerList = $httpResponse->headers();
        assertInstanceOf(HeaderList::class, $headerList);
        assertEquals(__METHOD__, $headerList->get('Host'));
        assertEquals('HTTP/1.1 404 Not Found', $httpResponse->statusLine());
        assertEquals(new HttpVersion(1, 1), $httpResponse->httpVersion());
        assertEquals(404, $httpResponse->statusCode());
        assertEquals('Not Found', $httpResponse->reasonPhrase());
        assertEquals(Http::STATUS_CLASS_ERROR_CLIENT, $httpResponse->statusCodeClass());
    }

    /**
     * @test
     */
    public function illegalStatusLineLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['Illegal Response',
                         'Host: ' . __METHOD__,
                         ''
                        ]
                )
        );
        assertNull($httpResponse->statusLine());
        assertNull($httpResponse->httpVersion());
        assertNull($httpResponse->statusCode());
        assertNull($httpResponse->reasonPhrase());
        assertNull($httpResponse->statusCodeClass());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function statusLineWithInvalidHttpVersionLeadsToEmptyResponse()
    {
        $httpResponse = $this->createResponse(
                Http::lines(
                        ['HTTP/400 102 Processing',
                         'Host: ' . __METHOD__,
                         ''
                        ]
                )
        );
        assertNull($httpResponse->statusLine());
        assertNull($httpResponse->httpVersion());
        assertNull($httpResponse->statusCode());
        assertNull($httpResponse->reasonPhrase());
        assertNull($httpResponse->statusCodeClass());
    }
}
