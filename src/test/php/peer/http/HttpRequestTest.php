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
use stubbles\peer\HeaderList;
use stubbles\peer\Stream;
use stubbles\peer\http\HttpResponse;
use stubbles\peer\http\HttpUri;
use stubbles\streams\InputStream;
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
        $stream  = NewInstance::stub(Stream::class)
                ->mapCalls(
                        ['in'  => NewInstance::of(InputStream::class),
                         'out' => $this->memoryOutputStream
                        ]
        );

        $uriCalls = [
            'openSocket' => $stream,
            'path'       => '/foo/resource',
            'hostname'   => 'example.com'
        ];
        if (null !== $queryString) {
            $uriCalls['hasQueryString'] = true;
            $uriCalls['queryString'] = $queryString;
        } else {
            $uriCalls['hasQueryString'] = false;
        }

        return HttpRequest::create(
                NewInstance::stub(HttpUri::class)->mapCalls($uriCalls),
                new HeaderList(['X-Binford' => 6100])
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequest()
    {
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->get()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest('foo=bar&baz=1')->get()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->get(5, HttpVersion::HTTP_1_0)
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->head()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest('foo=bar&baz=1')->head()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->head(5, HttpVersion::HTTP_1_0)
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->post('foobar')
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest('foo=bar&baz=1')->post('foobar')
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->post('foobar', 5, HttpVersion::HTTP_1_0)
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->post([])
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->post(['foo' => 'bar', 'ba z' => 'dum my'])
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->post(
                        ['foo' => 'bar', 'ba z' => 'dum my'],
                        5,
                        HttpVersion::HTTP_1_0
                )
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->put('foobar')
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest('foo=bar&baz=1')->put('foobar')
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->put('foobar', 5, HttpVersion::HTTP_1_0)
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->delete()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest('foo=bar&baz=1')->delete()
        );
        assertEquals(
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
        assertInstanceOf(
                HttpResponse::class,
                $this->createHttpRequest()->delete(5, HttpVersion::HTTP_1_0)
        );
        assertEquals(
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
