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
/**
 * Test for net\stubbles\peer\http\HttpUrl.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpUrlTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttp()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpUrl',
                                HttpUrl::fromString('http://example.net/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttps()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpUrl',
                                HttpUrl::fromString('https://example.net/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function createInstanceForOtherSchemeThrowsMalformedUrlException()
    {
        HttpUrl::fromString('invalid://example.net/');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function createInstanceFromInvalidUrlThrowsMalformedUrlException()
    {
        HttpUrl::fromString('invalid');
    }

    /**
     * @test
     */
    public function createInstanceFromEmptyStringReturnsNull()
    {
        $this->assertNull(HttpUrl::fromString(''));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function automaticallyAppensSlashAsPathIfNoPathSet()
    {
        $this->assertEquals('/',
                            HttpUrl::fromString('http://example.net')
                                   ->getPath()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttp()
    {
        $this->assertTrue(HttpUrl::fromString('http://example.net/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttp()
    {
        $this->assertTrue(HttpUrl::fromString('http://example.net:80/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttp()
    {
        $this->assertFalse(HttpUrl::fromString('http://example.net:8080/')
                                  ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttps()
    {
        $this->assertTrue(HttpUrl::fromString('https://example.net/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttps()
    {
        $this->assertTrue(HttpUrl::fromString('https://example.net:443/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttps()
    {
        $this->assertFalse(HttpUrl::fromString('https://example.net:8080/')
                                  ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturnsGivenPort()
    {
        $this->assertEquals(8080,
                            HttpUrl::fromString('http://example.net:8080/')
                                   ->getPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturns80IfSchemeIsHttp()
    {
        $this->assertEquals(80,
                            HttpUrl::fromString('http://example.net/')
                                   ->getPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function getPortReturns443IfSchemeIsHttp()
    {
        $this->assertEquals(443,
                            HttpUrl::fromString('https://example.net/')
                                   ->getPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpIfSchemeIsHttp()
    {
        $this->assertTrue(HttpUrl::fromString('http://example.net/')
                                 ->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpIfSchemeIsHttps()
    {
        $this->assertFalse(HttpUrl::fromString('https://example.net/')
                                  ->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpsIfSchemeIsHttps()
    {
        $this->assertTrue(HttpUrl::fromString('https://example.net/')
                                 ->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpsIfSchemeIsHttp()
    {
        $this->assertFalse(HttpUrl::fromString('http://example.net/')
                                  ->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsSameInstanceWhenTransposingHttpToHttp()
    {
        $httpUrl = HttpUrl::fromString('http://example.net/');
        $this->assertSame($httpUrl, $httpUrl->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpToHttps()
    {
        $httpUrl = HttpUrl::fromString('http://example.net/');
        $this->assertNotSame($httpUrl, $httpUrl->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpsLeavesEverythingExceptScheme()
    {
        $this->assertEquals('https://example.net:8080/foo.php?bar=baz#top',
                            HttpUrl::fromString('http://example.net:8080/foo.php?bar=baz#top')
                                   ->toHttps()
                                   ->asString()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsSameInstanceWhenTransposingHttpsToHttps()
    {
        $httpUrl = HttpUrl::fromString('https://example.net/');
        $this->assertSame($httpUrl, $httpUrl->toHttps());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsDifferentInstanceWhenTransposingHttpsToHttp()
    {
        $httpUrl = HttpUrl::fromString('https://example.net/');
        $this->assertNotSame($httpUrl, $httpUrl->toHttp());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function transposingToHttpLeavesEverythingExceptScheme()
    {
        $this->assertEquals('http://example.net:8080/foo.php?bar=baz#top',
                            HttpUrl::fromString('https://example.net:8080/foo.php?bar=baz#top')
                                   ->toHttp()
                                   ->asString()
        );
    }

    /**
     * @test
     */
    public function connectCreatesHttpConnection()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpConnection',
                                HttpUrl::fromString('http://example.net/')
                                       ->connect()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketForHttpDoesNotYieldSocketWithPrefix()
    {
        $this->assertNull(HttpUrl::fromString('http://example.net/')
                                 ->openSocket()
                                 ->getPrefix()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketForHttpsDoesYieldSocketWithPrefix()
    {
        $this->assertEquals('ssl://',
                            HttpUrl::fromString('https://example.net/')
                                   ->openSocket()
                                   ->getPrefix()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketUsesDefaultTimeout()
    {
        $this->assertEquals(5,
                            HttpUrl::fromString('http://example.net/')
                                   ->openSocket()
                                   ->getTimeout()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketUsesGivenTimeout()
    {
        $this->assertEquals(2,
                            HttpUrl::fromString('http://example.net/')
                                   ->openSocket(2)
                                   ->getTimeout()
        );
    }
}
?>