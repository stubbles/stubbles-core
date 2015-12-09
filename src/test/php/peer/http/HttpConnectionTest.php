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
use bovigo\callmap\NewInstance;
use stubbles\peer\Stream;
use stubbles\peer\http\HttpConnection;
use stubbles\peer\http\HttpUri;
use stubbles\peer\http\HttpResponse;
use stubbles\streams\InputStream;
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for stubbles\peer\http\HttpConnection.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\peer\http\HttpConnection
     */
    private $httpConnection;
    /**
     * @type  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
        $stream = NewInstance::stub(Stream::class)
                ->mapCalls(
                        ['in'  => NewInstance::of(InputStream::class),
                         'out' => $this->memoryOutputStream
                        ]
        );

        $httpUri = NewInstance::stub(HttpUri::class)
                ->mapCalls(
                        ['openSocket'     => $stream,
                         'path'           => '/foo/resource',
                         'hostname'       => 'example.com',
                         'hasQueryString' => true,
                         'queryString'    => 'foo=bar'
                        ]
        );
        $this->httpConnection = new HttpConnection($httpUri);
    }

    /**
     * @test
     */
    public function initializeGetRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->get()
        );
        assertEquals(
                Http::lines(
                        ['GET /foo/resource?foo=bar HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function initializeHeadRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                            ->asUserAgent('Stubbles HTTP Client')
                            ->referedFrom('http://example.com/')
                            ->withCookie(['foo' => 'bar baz'])
                            ->authorizedAs('user', 'pass')
                            ->usingHeader('X-Binford', 6100)
                            ->head()
        );
        assertEquals(
                Http::lines(
                        ['HEAD /foo/resource?foo=bar HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         'Connection: close',
                         ''
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function initializePostRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->post('foobar')
        );
        assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function initializePostRequestUsingPostValues()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->post(['foo' => 'bar', 'ba z' => 'dum my'])
        );
        assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         'Content-Type: application/x-www-form-urlencoded',
                         'Content-Length: 20',
                         '',
                         'foo=bar&ba+z=dum+my&'
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializePutRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->put('foobar')
        );
        assertEquals(
                Http::lines(
                        ['PUT /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         'Content-Length: 6',
                         '',
                         'foobar'
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializeDeleteRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->delete()
        );
        assertEquals(
                Http::lines(
                        ['DELETE /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'User-Agent: Stubbles HTTP Client',
                         'Referer: http://example.com/',
                         'Cookie: foo=bar+baz;',
                         'Authorization: BASIC ' . base64_encode('user:pass'),
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                $this->memoryOutputStream
        );
    }

    /**
     * @since  3.1.0
     * @test
     */
    public function functionShortcut()
    {
        assertInstanceOf(
                HttpConnection::class,
                \stubbles\peer\http('http://example.net/')
        );
    }
}
