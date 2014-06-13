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
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Test for stubbles\peer\http\HttpRequest.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpRequestTest extends \PHPUnit_Framework_TestCase
{
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
    }

    /**
     * creates instance to test
     *
     * @param   string  $queryString
     * @return  HttpRequest
     */
    private function createHttpRequest($queryString = null)
    {
        $mockHttpUri              = $this->getMock('stubbles\peer\http\HttpUri');
        $mockSocket               = $this->getMockBuilder('stubbles\peer\Socket')
                                         ->disableOriginalConstructor()
                                         ->getMock();
        $mockSocket->expects($this->any())
                   ->method('in')
                   ->will($this->returnValue($this->getMock('stubbles\streams\InputStream')));
        $mockSocket->expects(($this->any()))
                   ->method('out')
                   ->will($this->returnValue($this->memoryOutputStream));
        $mockHttpUri->expects($this->any())
                    ->method('openSocket')
                    ->will($this->returnValue($mockSocket));
        $mockHttpUri->expects($this->any())
                    ->method('getPath')
                    ->will($this->returnValue('/foo/resource'));
        if (null !== $queryString) {
            $mockHttpUri->expects($this->any())
                    ->method('hasQueryString')
                    ->will($this->returnValue(true));
            $mockHttpUri->expects($this->any())
                    ->method('getQueryString')
                    ->will($this->returnValue($queryString));
        }

        $mockHttpUri->expects($this->any())
                    ->method('getHost')
                    ->will($this->returnValue('example.com'));
        return HttpRequest::create($mockHttpUri,
                                   new HeaderList(['X-Binford' => 6100])
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->get()
        );
        $this->assertEquals(Http::line('GET /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function getWritesCorrectRequestWithQueryString()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest('foo=bar&baz=1')->get()
        );
        $this->assertEquals(Http::line('GET /foo/resource?foo=bar&baz=1 HTTP/1.1')
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->get(5, HttpVersion::HTTP_1_0)
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function getWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->get(5, 'invalid');
    }

    /**
     * @test
     */
    public function headWritesCorrectRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->head()
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
     * @since   2.1.2
     * @test
     */
    public function headWritesCorrectRequestWithQueryString()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest('foo=bar&baz=1')->head()
        );
        $this->assertEquals(Http::line('HEAD /foo/resource?foo=bar&baz=1 HTTP/1.1')
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->head(5, HttpVersion::HTTP_1_0)
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function headWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->head(5, 'invalid');
    }

    /**
     * @test
     */
    public function postWritesCorrectRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->post('foobar')
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
     * @since   2.1.2
     * @test
     */
    public function postIgnoresQueryString()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest('foo=bar&baz=1')->post('foobar')
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->post('foobar', 5, HttpVersion::HTTP_1_0)
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->post([])
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->post(['foo' => 'bar', 'ba z' => 'dum my'])
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->post(['foo' => 'bar', 'ba z' => 'dum my'], 5, HttpVersion::HTTP_1_0)
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function postWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->post('foobar', 5, 'invalid');
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function putWritesCorrectRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->put('foobar')
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
     * @since   2.1.2
     * @test
     */
    public function putIgnoresQueryString()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest('foo=bar&baz=1')->put('foobar')
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->put('foobar', 5, HttpVersion::HTTP_1_0)
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function putWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->put('foobar', 5, 'invalid');
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function deleteWritesCorrectRequest()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->delete()
        );
        $this->assertEquals(Http::line('DELETE /foo/resource HTTP/1.1')
                          . Http::line('Host: example.com')
                          . Http::line('X-Binford: 6100')
                          . Http::emptyLine(),
                            $this->memoryOutputStream->getBuffer()
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function deleteIgnoresQueryString()
    {
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest('foo=bar&baz=1')->delete()
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
        $this->assertInstanceOf('stubbles\peer\http\HttpResponse',
                                $this->createHttpRequest()->delete(5, HttpVersion::HTTP_1_0)
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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function deleteWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->delete(5, 'invalid');
    }
}
