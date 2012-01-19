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
use net\stubbles\peer\ParsedUrl;
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
    protected $httpRequest;
    /**
     * memory stream to write http request to
     *
     * @type  MemoryOutputStream
     */
    protected $memoryOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryOutputStream = new MemoryOutputStream();
        $mockHttpUrl              = $this->getMock('net\\stubbles\\peer\\http\\HttpUrlContainer');
        $mockSocket               = $this->getMock('net\\stubbles\\peer\\Socket', array(), array('example.com'));
        $mockSocket->expects($this->any())
                   ->method('getInputStream')
                   ->will($this->returnValue($this->getMock('net\\stubbles\\streams\\InputStream')));
        $mockSocket->expects(($this->any()))
                   ->method('getOutputStream')
                   ->will($this->returnValue($this->memoryOutputStream));
        $mockHttpUrl->expects($this->any())
                    ->method('openSocket')
                    ->will($this->returnValue($mockSocket));
        $mockHttpUrl->expects($this->any())
                    ->method('getPath')
                    ->will($this->returnValue('/'));
        $mockHttpUrl->expects($this->any())
                    ->method('getHost')
                    ->will($this->returnValue('example.com'));
        $this->httpRequest = HttpRequest::create($mockHttpUrl,
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
        $this->assertEquals(Http::line('GET / HTTP/1.1')
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
        $this->assertEquals(Http::line('GET / HTTP/1.0')
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
        $this->assertEquals(Http::line('HEAD / HTTP/1.1')
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
        $this->assertEquals(Http::line('HEAD / HTTP/1.0')
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
        $this->assertEquals(Http::line('POST / HTTP/1.1')
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
        $this->assertEquals(Http::line('POST / HTTP/1.0')
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
        $this->assertEquals(Http::line('POST / HTTP/1.1')
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
        $this->assertEquals(Http::line('POST / HTTP/1.1')
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
        $this->assertEquals(Http::line('POST / HTTP/1.0')
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
}
?>