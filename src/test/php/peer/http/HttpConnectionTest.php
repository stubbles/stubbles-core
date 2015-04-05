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
     * @type  HttpConnection
     */
    private $httpConnection;
    /**
     * URI instance to be used
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockHttpUri;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
        $this->mockHttpUri        = $this->getMock('stubbles\peer\http\HttpUri');
        $mockStream               = $this->getMockBuilder('stubbles\peer\Stream')
                ->disableOriginalConstructor()
                ->getMock();
        $mockStream->method('setTimeout')->will(returnSelf());
        $mockStream->method('in')
                ->will(returnValue($this->getMock('stubbles\streams\InputStream')));
        $mockStream->method('out')
                ->will(returnValue($this->memoryOutputStream));
        $this->mockHttpUri->method('openSocket')
                ->with(equalTo(2))
                ->will(returnValue($mockStream));
        $this->mockHttpUri->method('path')->will(returnValue('/foo/resource'));
        $this->mockHttpUri->method('hostname')->will(returnValue('example.com'));
        $this->httpConnection = new HttpConnection($this->mockHttpUri);
    }

    /**
     * @test
     */
    public function initializeGetRequest()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->get()
        );
        $this->assertEquals(
                Http::lines(
                        ['GET /foo/resource HTTP/1.1',
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                            ->asUserAgent('Stubbles HTTP Client')
                            ->referedFrom('http://example.com/')
                            ->withCookie(['foo' => 'bar baz'])
                            ->authorizedAs('user', 'pass')
                            ->usingHeader('X-Binford', 6100)
                            ->head()
        );
        $this->assertEquals(
                Http::lines(
                        ['HEAD /foo/resource HTTP/1.1',
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
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function initializePostRequest()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->post('foobar')
        );
        $this->assertEquals(
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
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function initializePostRequestUsingPostValues()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->post(['foo' => 'bar', 'ba z' => 'dum my'])
        );
        $this->assertEquals(
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
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializePutRequest()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->put('foobar')
        );
        $this->assertEquals(
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
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializeDeleteRequest()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->httpConnection->timeout(2)
                        ->asUserAgent('Stubbles HTTP Client')
                        ->referedFrom('http://example.com/')
                        ->withCookie(['foo' => 'bar baz'])
                        ->authorizedAs('user', 'pass')
                        ->usingHeader('X-Binford', 6100)
                        ->delete()
        );
        $this->assertEquals(
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
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since  3.1.0
     * @test
     */
    public function functionShortcut()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpConnection',
                \stubbles\peer\http('http://example.net/')
        );
    }
}
