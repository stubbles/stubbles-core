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
use net\stubbles\peer\HeaderList;
use net\stubbles\peer\Socket;
use net\stubbles\peer\ParsedUri;
use net\stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for net\stubbles\peer\http\HttpRequest.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpRequestTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  HttpRequest
     */
    private $httpRequest;
    /**
     * memory stream to write http request to
     *
     * @type  MemoryOutputStream
     */
    private $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
        $mockHttpUri              = $this->getMock('net\\stubbles\\peer\\http\\HttpUri');
        $mockSocket               = $this->getMock('net\\stubbles\\peer\\Socket', array(), array('example.com'));
        $mockSocket->expects($this->any())
                   ->method('getInputStream')
                   ->will($this->returnValue($this->getMock('net\\stubbles\\streams\\InputStream')));
        $mockSocket->expects(($this->any()))
                   ->method('getOutputStream')
                   ->will($this->returnValue($this->memoryOutputStream));
        $mockHttpUri->expects($this->any())
                    ->method('openSocket')
                    ->will($this->returnValue($mockSocket));
        $mockHttpUri->expects($this->any())
                    ->method('getPath')
                    ->will($this->returnValue('/foo/resource'));
        $mockHttpUri->expects($this->any())
                    ->method('getHost')
                    ->will($this->returnValue('example.com'));
        $this->httpRequest = HttpRequest::create($mockHttpUri,
                                                 new HeaderList(array('X-Binford' => 6100))
                             );


    }

    /**
     * @test
     */
    public function getWritesCorrectRequest()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->get()
        );
        $this->assertEquals(Http::line('GET /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->get(5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('GET /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function getWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->httpRequest->get(5, 'invalid');
    }

    /**
     * @test
     */
    public function headWritesCorrectRequest()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->head()
        );
        $this->assertEquals(Http::line('HEAD /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Connection: close')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function headWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->head(5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('HEAD /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Connection: close')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function headWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->httpRequest->head(5, 'invalid');
    }

    /**
     * @test
     */
    public function postWritesCorrectRequest()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->post('foobar')
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
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
    public function postWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->post('foobar', 5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
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
    public function postWritesCorrectRequestUsingEmptyPostValues()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->post(array())
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Type: application/x-www-form-urlencoded')
                          . Http::line('Content-Length: 0')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingPostValues()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->post(array('foo' => 'bar', 'ba z' => 'dum my'))
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Type: application/x-www-form-urlencoded')
                          . Http::line('Content-Length: 20')
                          . Http::emptyLine()
                          . 'foo=bar&ba+z=dum+my&',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingPostValuesWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->post(array('foo' => 'bar', 'ba z' => 'dum my'), 5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('POST /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Type: application/x-www-form-urlencoded')
                          . Http::line('Content-Length: 20')
                          . Http::emptyLine()
                          . 'foo=bar&ba+z=dum+my&',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function postWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->httpRequest->post('foobar', 5, 'invalid');
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function putWritesCorrectRequest()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->put('foobar')
        );
        $this->assertEquals(Http::line('PUT /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Length: 6')
                          . Http::emptyLine()
                          . 'foobar',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function putWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->put('foobar', 5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('PUT /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::line('Content-Length: 6')
                          . Http::emptyLine()
                          . 'foobar',
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.0.0
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function putWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->httpRequest->put('foobar', 5, 'invalid');
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function deleteWritesCorrectRequest()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->delete()
        );
        $this->assertEquals(Http::line('DELETE /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function deleteWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpResponse',
                                $this->httpRequest->delete(5, Http::VERSION_1_0)
        );
        $this->assertEquals(Http::line('DELETE /foo/resource HTTP/1.0')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.0.0
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function deleteWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->httpRequest->delete(5, 'invalid');
    }
}
?>