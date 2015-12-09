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
/**
 * Test for stubbles\peer\http\HttpUri.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttp()
    {
        assertInstanceOf(
                HttpUri::class,
                HttpUri::fromString('http://example.net/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttps()
    {
        assertInstanceOf(
                HttpUri::class,
                HttpUri::fromString('https://example.net/')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function canNotCreateHttpUriFromInvalidHost()
    {
        HttpUri::fromString('http://:');
    }

    /**
     * @since  2.0.0
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function createInstanceForOtherSchemeThrowsMalformedUriException()
    {
        HttpUri::fromString('invalid://example.net/');
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function createInstanceFromInvalidUriThrowsMalformedUriException()
    {
        HttpUri::fromString('invalid');
    }

    /**
     * @test
     */
    public function createInstanceFromEmptyStringReturnsNull()
    {
        assertNull(HttpUri::fromString(''));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function automaticallyAppensSlashAsPathIfNoPathSet()
    {
        assertEquals(
                '/',
                HttpUri::fromString('http://example.net')->path()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttp()
    {
        assertTrue(
                HttpUri::fromString('http://example.net/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttp()
    {
        assertTrue(
                HttpUri::fromString('http://example.net:80/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttp()
    {
        assertFalse(
                HttpUri::fromString('http://example.net:8080/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttps()
    {
        assertTrue(
                HttpUri::fromString('https://example.net/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttps()
    {
        assertTrue(
                HttpUri::fromString('https://example.net:443/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttps()
    {
        assertFalse(
                HttpUri::fromString('https://example.net:8080/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturnsGivenPort()
    {
        assertEquals(
                8080,
                HttpUri::fromString('http://example.net:8080/')->port()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturns80IfSchemeIsHttp()
    {
        assertEquals(
                80,
                HttpUri::fromString('http://example.net/')->port()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturns443IfSchemeIsHttp()
    {
        assertEquals(
                443,
                HttpUri::fromString('https://example.net/')->port()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpIfSchemeIsHttp()
    {
        assertTrue(
                HttpUri::fromString('http://example.net/')->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpIfSchemeIsHttps()
    {
        assertFalse(
                HttpUri::fromString('https://example.net/')->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpsIfSchemeIsHttps()
    {
        assertTrue(
                HttpUri::fromString('https://example.net/')->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpsIfSchemeIsHttp()
    {
        assertFalse(
                HttpUri::fromString('http://example.net/')->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsSameInstanceWhenTransposingHttpToHttp()
    {
        $httpUri = HttpUri::fromString('http://example.net/');
        assertSame($httpUri, $httpUri->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpToHttps()
    {
        $httpUri = HttpUri::fromString('http://example.net/');
        assertNotSame($httpUri, $httpUri->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpsLeavesEverythingExceptSchemeAndPort()
    {
        assertEquals(
                'https://example.net/foo.php?bar=baz#top',
                HttpUri::fromString('http://example.net:8080/foo.php?bar=baz#top')
                       ->toHttps()
                       ->asString()
        );
    }

    /**
     * @since  4.0.2
     * @test
     */
    public function transposingToHttpChangesPort()
    {
        assertEquals(
                'http://example.net:8080/foo.php?bar=baz#top',
                HttpUri::fromString('http://example.net:8080/foo.php?bar=baz#top')
                       ->toHttp()
                       ->asString()
        );
    }

    /**
     * @since  4.1.1
     * @test
     */
    public function transposingToHttpUsesDefaultPortToDefaultIfDefault()
    {
        assertEquals(
                'http://example.net/foo.php?bar=baz#top',
                HttpUri::fromString('https://example.net/foo.php?bar=baz#top')
                       ->toHttp()
                       ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsSameInstanceWhenTransposingHttpsToHttps()
    {
        $httpUri = HttpUri::fromString('https://example.net/');
        assertSame($httpUri, $httpUri->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpsToHttp()
    {
        $httpUri = HttpUri::fromString('https://example.net/');
        assertNotSame($httpUri, $httpUri->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpLeavesEverythingExceptSchemeAndPort()
    {
        assertEquals(
                'http://example.net/foo.php?bar=baz#top',
                HttpUri::fromString('https://example.net:8080/foo.php?bar=baz#top')
                       ->toHttp()
                       ->asString()
        );
    }

    /**
     * @since  4.0.2
     * @test
     */
    public function transposingToHttpsWithDifferentPort()
    {
        assertEquals(
                'https://example.net:8080/foo.php?bar=baz#top',
                HttpUri::fromString('https://example.net:8080/foo.php?bar=baz#top')
                       ->toHttps()
                       ->asString()
        );
    }

    /**
     * @since  4.1.1
     * @test
     */
    public function transposingToHttpsUsesDefaultPortIfIsDefaultPort()
    {
        assertEquals(
                'https://example.net/foo.php?bar=baz#top',
                HttpUri::fromString('http://example.net/foo.php?bar=baz#top')
                       ->toHttps()
                       ->asString()
        );
    }

    /**
     * @test
     */
    public function connectCreatesHttpConnection()
    {
        assertInstanceOf(
                HttpConnection::class,
                HttpUri::fromString('http://example.net/')->connect()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function createSocketForHttpDoesNotYieldSocketWithSecureConnection()
    {
        assertFalse(
                HttpUri::fromString('http://example.net/')
                       ->createSocket()
                       ->usesSsl()
        );
    }
    /**
     * @since  2.0.0
     * @test
     */
    public function createSocketForHttpsDoesYieldSocketWithSecureConnection()
    {
        assertTrue(
                HttpUri::fromString('https://example.net/')
                       ->createSocket()
                       ->usesSsl()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketUsesDefaultTimeout()
    {
        assertEquals(
               5,
               HttpUri::fromString('http://example.net/')
                      ->openSocket()
                      ->timeout()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketUsesGivenTimeout()
    {
        assertEquals(
                2,
                HttpUri::fromString('http://example.net/')
                       ->openSocket(2)
                       ->timeout()
        );
    }

    /**
     * @since  4.0.0
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function createInstanceWithUserInfoThrowsMalformedUriExceptionForDefaultRfc()
    {
        HttpUri::fromString('http://user:password@example.net/');
    }

    /**
     * @since  4.0.0
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function createInstanceWithUserInfoThrowsMalformedUriExceptionForRfc7230()
    {
        HttpUri::fromString('http://user:password@example.net/', Http::RFC_7230);
    }

    /**
     * @since  4.0.0
     * @test
     */
    public function createInstanceWithUserInfoThrowsNoMalformedUriExceptionForRfc2616()
    {
        $uri = 'http://user:password@example.net/';
        assertEquals(
                $uri,
                HttpUri::fromString($uri, Http::RFC_2616)->asString()
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function castFromInstanceReturnsInstance()
    {
        $uri = HttpUri::fromString('http://example.net/');
        assertSame($uri, HttpUri::castFrom($uri));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function castFromStringeReturnsInstance()
    {
        $uri = HttpUri::fromString('http://example.net/');
        assertEquals($uri, HttpUri::castFrom('http://example.net/'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Uri must be a string containing a HTTP URI or an instance of stubbles\peer\http\HttpUri, but was stdClass
     * @since  4.0.0
     */
    public function castFromOtherThrowsIllegalArgumentException()
    {
        HttpUri::castFrom(new \stdClass());
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     * @since  4.0.0
     */
    public function createFromPartsWithInvalidSchemeThrowsMalformedUriException()
    {
        HttpUri::fromParts('foo', 'localhost');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function createFromPartsWithDefaultPortAndPathAndNoQueryString()
    {
        assertEquals(
                'http://localhost/',
                HttpUri::fromParts('http', 'localhost')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function createFromAllParts()
    {
        assertEquals(
                'https://localhost:8080/index.php?foo=bar',
                HttpUri::fromParts('https', 'localhost', 8080, '/index.php', 'foo=bar')
        );
    }
    /**
     * @test
     * @since  4.0.0
     */
    public function fromPartsReturnsInstanceOfHttpUri()
    {
        assertInstanceOf(
                HttpUri::class,
                HttpUri::fromParts('https', 'localhost', 8080, '/index.php', 'foo=bar')
        );
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function withPathExchangesPathCompletely()
    {
        assertEquals(
                'http://example.org/bar',
                HttpUri::fromString('http://example.org/foo')->withPath('/bar')
        );
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function withPathReturnsNewInstance()
    {
        $uri = HttpUri::fromString('http://example.org/foo');
        assertNotSame(
                $uri,
                $uri->withPath('/bar')
        );
    }
}
