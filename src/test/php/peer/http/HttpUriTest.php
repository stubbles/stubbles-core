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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpUri',
                HttpUri::fromString('http://example.net/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttps()
    {
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpUri',
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
        $this->assertNull(HttpUri::fromString(''));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function automaticallyAppensSlashAsPathIfNoPathSet()
    {
        $this->assertEquals(
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
        $this->assertTrue(
                HttpUri::fromString('http://example.net/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttp()
    {
        $this->assertTrue(
                HttpUri::fromString('http://example.net:80/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttp()
    {
        $this->assertFalse(
                HttpUri::fromString('http://example.net:8080/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttps()
    {
        $this->assertTrue(
                HttpUri::fromString('https://example.net/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttps()
    {
        $this->assertTrue(
                HttpUri::fromString('https://example.net:443/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttps()
    {
        $this->assertFalse(
                HttpUri::fromString('https://example.net:8080/')->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturnsGivenPort()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertTrue(
                HttpUri::fromString('http://example.net/')->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpIfSchemeIsHttps()
    {
        $this->assertFalse(
                HttpUri::fromString('https://example.net/')->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpsIfSchemeIsHttps()
    {
        $this->assertTrue(
                HttpUri::fromString('https://example.net/')->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpsIfSchemeIsHttp()
    {
        $this->assertFalse(
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
        $this->assertSame($httpUri, $httpUri->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpToHttps()
    {
        $httpUri = HttpUri::fromString('http://example.net/');
        $this->assertNotSame($httpUri, $httpUri->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpsLeavesEverythingExceptSchemeAndPort()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertSame($httpUri, $httpUri->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpsToHttp()
    {
        $httpUri = HttpUri::fromString('https://example.net/');
        $this->assertNotSame($httpUri, $httpUri->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpLeavesEverythingExceptSchemeAndPort()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpConnection',
                HttpUri::fromString('http://example.net/')->connect()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketForHttpDoesNotYieldSocketWithSecureConnection()
    {
        $this->assertFalse(
                HttpUri::fromString('http://example.net/')
                       ->openSocket()
                       ->usesSsl()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketForHttpsDoesYieldSocketWithSecureConnection()
    {
        $this->assertTrue(
                HttpUri::fromString('https://example.net/')
                       ->openSocket()
                       ->usesSsl()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketUsesDefaultTimeout()
    {
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertSame($uri, HttpUri::castFrom($uri));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function castFromStringeReturnsInstance()
    {
        $uri = HttpUri::fromString('http://example.net/');
        $this->assertEquals($uri, HttpUri::castFrom('http://example.net/'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(
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
        $this->assertEquals(
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
        $this->assertInstanceOf(
                'stubbles\peer\http\HttpUri',
                HttpUri::fromParts('https', 'localhost', 8080, '/index.php', 'foo=bar')
        );
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function withPathExchangesPathCompletely()
    {
        $this->assertEquals(
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
        $this->assertNotSame(
                $uri,
                $uri->withPath('/bar')
        );
    }
}
