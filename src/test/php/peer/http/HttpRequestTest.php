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
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\peer\http\HttpRequest.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * memory to write http request to
     *
     * @type  string
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = '';
    }

    /**
     * creates instance to test
     *
     * @param   string  $queryString
     * @return  HttpRequest
     */
    private function createHttpRequest($queryString = null)
    {
        $socket   = NewInstance::stub(Stream::class)->mapCalls([
                'write' => function($line) { $this->memory .= $line;}
        ]);

        $uriCalls = [
            'openSocket' => $socket,
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
        $this->createHttpRequest()->get();
        assert(
                $this->memory,
                equals(Http::lines(
                        'GET /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function getWritesCorrectRequestWithQueryString()
    {
        $this->createHttpRequest('foo=bar&baz=1')->get();
        assert(
                $this->memory,
                equals(Http::lines(
                        'GET /foo/resource?foo=bar&baz=1 HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
        );
    }

    /**
     * @test
     */
    public function getWritesCorrectRequestWithVersion()
    {
        $this->createHttpRequest()->get(5, HttpVersion::HTTP_1_0);
        assert(
                $this->memory,
                equals(Http::lines(
                        'GET /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
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
        $this->createHttpRequest()->head();
        assert(
                $this->memory,
                equals(Http::lines(
                        'HEAD /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Connection: close',
                        ''
                ))
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function headWritesCorrectRequestWithQueryString()
    {
        $this->createHttpRequest('foo=bar&baz=1')->head();
        assert(
                $this->memory,
                equals(Http::lines(
                        'HEAD /foo/resource?foo=bar&baz=1 HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Connection: close',
                        ''
                ))
        );
    }

    /**
     * @test
     */
    public function headWritesCorrectRequestWithVersion()
    {
        $this->createHttpRequest()->head(5, HttpVersion::HTTP_1_0);
        assert(
                $this->memory,
                equals(Http::lines(
                        'HEAD /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Connection: close',
                        ''
                ))
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
        $this->createHttpRequest()->post('foobar');
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function postIgnoresQueryString()
    {
        $this->createHttpRequest('foo=bar&baz=1')->post('foobar');
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestWithVersion()
    {
        $this->createHttpRequest()->post('foobar', 5, HttpVersion::HTTP_1_0);
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingEmptyPostValues()
    {
        $this->createHttpRequest()->post([]);
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length: 0',
                        ''
                ))
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingPostValues()
    {
        $this->createHttpRequest()->post(['foo' => 'bar', 'ba z' => 'dum my']);
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length: 20',
                        '',
                        'foo=bar&ba+z=dum+my&'
                ))
        );
    }

    /**
     * @test
     */
    public function postWritesCorrectRequestUsingPostValuesWithVersion()
    {
        $this->createHttpRequest()->post(
                ['foo' => 'bar', 'ba z' => 'dum my'],
                5,
                HttpVersion::HTTP_1_0
        );
        assert(
                $this->memory,
                equals(Http::lines(
                        'POST /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length: 20',
                        '',
                        'foo=bar&ba+z=dum+my&'
                ))
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
        $this->createHttpRequest()->put('foobar');
        assert(
                $this->memory,
                equals(Http::lines(
                        'PUT /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function putIgnoresQueryString()
    {
        $this->createHttpRequest('foo=bar&baz=1')->put('foobar');
        assert(
                $this->memory,
                equals(Http::lines(
                        'PUT /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
        );
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function putWritesCorrectRequestWithVersion()
    {
        $this->createHttpRequest()->put('foobar', 5, HttpVersion::HTTP_1_0);
        assert(
                $this->memory,
                equals(Http::lines(
                        'PUT /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        'Content-Length: 6',
                        '',
                        'foobar'
                ))
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
        $this->createHttpRequest()->delete();
        assert(
                $this->memory,
                equals(Http::lines(
                        'DELETE /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
        );
    }

    /**
     * @since   2.1.2
     * @test
     */
    public function deleteIgnoresQueryString()
    {
        $this->createHttpRequest('foo=bar&baz=1')->delete();
        assert(
                $this->memory,
                equals(Http::lines(
                        'DELETE /foo/resource HTTP/1.1',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
        );
    }

    /**
     * @since   2.0.0
     * @test
     */
    public function deleteWritesCorrectRequestWithVersion()
    {
        $this->createHttpRequest()->delete(5, HttpVersion::HTTP_1_0);
        assert(
                $this->memory,
                equals(Http::lines(
                        'DELETE /foo/resource HTTP/1.0',
                        'Host: example.com',
                        'X-Binford: 6100',
                        ''
                ))
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
