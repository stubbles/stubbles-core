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
        $mockHttpUri = $this->getMock('stubbles\peer\http\HttpUri');
        $mockStream  = $this->getMockBuilder('stubbles\peer\Stream')
                ->disableOriginalConstructor()
                ->getMock();
        $mockStream->method('setTimeout')->will(returnSelf());
        $mockStream->method('in')
                ->will(returnValue($this->getMock('stubbles\streams\InputStream')));
        $mockStream->method('out')
                ->will(returnValue($this->memoryOutputStream));
        $mockHttpUri->method('openSocket')
                ->will(returnValue($mockStream));
        $mockHttpUri->method('path')->will(returnValue('/foo/resource'));
        if (null !== $queryString) {
            $mockHttpUri->method('hasQueryString')->will(returnValue(true));
            $mockHttpUri->method('queryString')->will(returnValue($queryString));
        }

        $mockHttpUri->method('hostname')->will(returnValue('example.com'));
        return HttpRequest::create(
                $mockHttpUri,
                new HeaderList(['X-Binford' => 6100])
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequest()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->get()
        );
        $this->assertEquals(
                Http::lines(
                        ['GET /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         '',
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function getWritesCorrectRequestWithQueryString()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest('foo=bar&baz=1')->get()
        );
        $this->assertEquals(
                Http::lines(
                        ['GET /foo/resource?foo=bar&baz=1 HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->get(5, HttpVersion::HTTP_1_0)
        );
        $this->assertEquals(
                Http::lines(
                        ['GET /foo/resource HTTP/1.0',
                         'Host: example.com',
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->head()
        );
        $this->assertEquals(
                Http::lines(
                        ['HEAD /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         'Connection: close',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function headWritesCorrectRequestWithQueryString()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest('foo=bar&baz=1')->head()
        );
        $this->assertEquals(
                Http::lines(
                        ['HEAD /foo/resource?foo=bar&baz=1 HTTP/1.1',
                         'Host: example.com',
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
    public function headWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->head(5, HttpVersion::HTTP_1_0)
        );
        $this->assertEquals(
                Http::lines(
                        ['HEAD /foo/resource HTTP/1.0',
                         'Host: example.com',
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
     * @expectedException  InvalidArgumentException
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->post('foobar')
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         'Content-Length: 6',
                         '',
                         'foobar',
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function postIgnoresQueryString()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest('foo=bar&baz=1')->post('foobar')
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
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
    public function postWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->post('foobar', 5, HttpVersion::HTTP_1_0)
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.0',
                         'Host: example.com',
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
    public function postWritesCorrectRequestUsingEmptyPostValues()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->post([])
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         'Content-Type: application/x-www-form-urlencoded',
                         'Content-Length: 0',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingPostValues()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->post(['foo' => 'bar', 'ba z' => 'dum my'])
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.1',
                         'Host: example.com',
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
     * @test
     */
    public function postWritesCorrectRequestUsingPostValuesWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->post(
                        ['foo' => 'bar', 'ba z' => 'dum my'],
                        5,
                        HttpVersion::HTTP_1_0
                )
        );
        $this->assertEquals(
                Http::lines(
                        ['POST /foo/resource HTTP/1.0',
                         'Host: example.com',
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
     * @test
     * @expectedException  InvalidArgumentException
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->put('foobar')
        );
        $this->assertEquals(
                Http::lines(
                        ['PUT /foo/resource HTTP/1.1',
                         'Host: example.com',
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
     * @since   2.1.2
     * @test
     */
    public function putIgnoresQueryString()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest('foo=bar&baz=1')->put('foobar')
        );
        $this->assertEquals(
                Http::lines(
                        ['PUT /foo/resource HTTP/1.1',
                         'Host: example.com',
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
     * @since   2.0.0
     * @test
     */
    public function putWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->put('foobar', 5, HttpVersion::HTTP_1_0)
        );
        $this->assertEquals(
                Http::lines(
                        ['PUT /foo/resource HTTP/1.0',
                         'Host: example.com',
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
     * @since   2.0.0
     * @test
     * @expectedException  InvalidArgumentException
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->delete()
        );
        $this->assertEquals(
                Http::lines(
                        ['DELETE /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         '',
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function deleteIgnoresQueryString()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest('foo=bar&baz=1')->delete()
        );
        $this->assertEquals(
                Http::lines(
                        ['DELETE /foo/resource HTTP/1.1',
                         'Host: example.com',
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function deleteWritesCorrectRequestWithVersion()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpResponse',
                $this->createHttpRequest()->delete(5, HttpVersion::HTTP_1_0)
        );
        $this->assertEquals(
                Http::lines(
                        ['DELETE /foo/resource HTTP/1.0',
                         'Host: example.com',
                         'X-Binford: 6100',
                         ''
                        ]
                ),
                (string) $this->memoryOutputStream
        );
    }

    /**
     * @since   2.0.0
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function deleteWithInvalidHttpVersionThrowsIllegalArgumentException()
    {
        $this->createHttpRequest()->delete(5, 'invalid');
    }
}
