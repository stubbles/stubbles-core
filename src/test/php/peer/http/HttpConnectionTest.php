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
        $mockSocket               = $this->getMock('stubbles\peer\Socket', [], ['example.com']);
        $mockSocket->expects($this->any())
                   ->method('in')
                   ->will($this->returnValue($this->getMock('stubbles\streams\InputStream')));
        $mockSocket->expects(($this->any()))
                   ->method('out')
                   ->will($this->returnValue($this->memoryOutputStream));
        $this->mockHttpUri->expects($this->any())
                          ->method('openSocket')
                          ->with($this->equalTo(2))
                          ->will($this->returnValue($mockSocket));
        $this->mockHttpUri->expects($this->any())
                          ->method('getPath')
                          ->will($this->returnValue('/foo/resource'));
        $this->mockHttpUri->expects($this->any())
                          ->method('getHost')
                          ->will($this->returnValue('example.com'));
        $this->httpConnection = new HttpConnection($this->mockHttpUri);
    }

    /**
     * @test
     */
    public function initializeGetRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->get()
        );
        $this->assertEquals(Http::line('GET /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function initializeHeadRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->head()
        );
        $this->assertEquals(Http::line('HEAD /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::line('Connection: close')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function initializePostRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->post('foobar')
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Length: 6')
                          . Http::emptyLine()
                          . 'foobar',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function initializePostRequestUsingPostValues()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->post(['foo' => 'bar', 'ba z' => 'dum my'])
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Type: application/x-www-form-urlencoded')
                          . Http::line('Content-Length: 20')
                          . Http::emptyLine()
                          . 'foo=bar&ba+z=dum+my&',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializePutRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->put('foobar')
        );
        $this->assertEquals(Http::line('PUT /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Length: 6')
                          . Http::emptyLine()
                          . 'foobar',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initializeDeleteRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->httpConnection->timeout(2)
                                                     ->asUserAgent('Stubbles HTTP Client')
                                                     ->referedFrom('http://example.com/')
                                                     ->withCookie(['foo' => 'bar baz'])
                                                     ->authorizedAs('user', 'pass')
                                                     ->usingHeader('X-Binford', 6100)
                                                     ->delete()
        );
        $this->assertEquals(Http::line('DELETE /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('User-Agent: Stubbles HTTP Client')
                          . Http::line('Referer: http://example.com/')
                          . Http::line('Cookie: foo=bar+baz;')
                          . Http::line('Authorization: BASIC ' . base64_encode('user:pass'))
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since  3.1.0
     * @test
     */
    public function functionShortcut()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpConnection',
                                \stubbles\peer\http('http://example.net/')
        );
    }
}
