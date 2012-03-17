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
 * Test for net\stubbles\peer\http\HttpUri.
 *
 * @group  peer
 * @group  peer_http
 */
class HttpUriTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttp()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpUri',
                                HttpUri::fromString('http://example.net/')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateInstanceForSchemeHttps()
    {
        $this->assertInstanceOf('net\\stubbles\\peer\\http\\HttpUri',
                                HttpUri::fromString('https://example.net/')
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateHttpUriFromInvalidHost()
    {
        HttpUri::fromString('http://:');
    }

    /**
     * @since  2.0.0
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function createInstanceForOtherSchemeThrowsMalformedUriException()
    {
        HttpUri::fromString('invalid://example.net/');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
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
        $this->assertEquals('/',
                            HttpUri::fromString('http://example.net')
                                   ->getPath()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttp()
    {
        $this->assertTrue(HttpUri::fromString('http://example.net/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttp()
    {
        $this->assertTrue(HttpUri::fromString('http://example.net:80/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttp()
    {
        $this->assertFalse(HttpUri::fromString('http://example.net:8080/')
                                  ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfNoPortGivenInSchemeHttps()
    {
        $this->assertTrue(HttpUri::fromString('https://example.net/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDefaultPortIfDefaultPortGivenInSchemeHttps()
    {
        $this->assertTrue(HttpUri::fromString('https://example.net:443/')
                                 ->hasDefaultPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function doesNotHaveDefaultPortIfOtherPortGivenInSchemeHttps()
    {
        $this->assertFalse(HttpUri::fromString('https://example.net:8080/')
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
                            HttpUri::fromString('http://example.net:8080/')
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
                            HttpUri::fromString('http://example.net/')
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
                            HttpUri::fromString('https://example.net/')
                                   ->getPort()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpIfSchemeIsHttp()
    {
        $this->assertTrue(HttpUri::fromString('http://example.net/')
                                 ->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpIfSchemeIsHttps()
    {
        $this->assertFalse(HttpUri::fromString('https://example.net/')
                                  ->isHttp()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isHttpsIfSchemeIsHttps()
    {
        $this->assertTrue(HttpUri::fromString('https://example.net/')
                                 ->isHttps()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function isNotHttpsIfSchemeIsHttp()
    {
        $this->assertFalse(HttpUri::fromString('http://example.net/')
                                  ->isHttps()
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
    public function transposingToHttpsLeavesEverythingExceptScheme()
    {
        $this->assertEquals('https://example.net:8080/foo.php?bar=baz#top',
                            HttpUri::fromString('http://example.net:8080/foo.php?bar=baz#top')
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
    public function transposingToHttpLeavesEverythingExceptScheme()
    {
        $this->assertEquals('http://example.net:8080/foo.php?bar=baz#top',
                            HttpUri::fromString('https://example.net:8080/foo.php?bar=baz#top')
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
                                HttpUri::fromString('http://example.net/')
                                       ->connect()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function openSocketForHttpDoesNotYieldSocketWithPrefix()
    {
        $this->assertNull(HttpUri::fromString('http://example.net/')
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
                            HttpUri::fromString('https://example.net/')
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
                            HttpUri::fromString('http://example.net/')
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
                            HttpUri::fromString('http://example.net/')
                                   ->openSocket(2)
                                   ->getTimeout()
        );
    }
}
?>