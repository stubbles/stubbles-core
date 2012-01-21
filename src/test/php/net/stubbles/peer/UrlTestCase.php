<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
/**
 * Test for net\stubbles\peer\Url.
 *
 * @group  peer
 */
class UrlTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function canNotCreateUrlWithoutScheme()
    {
        Url::fromString('stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function canNotCreateUrlWithInvalidScheme()
    {
        Url::fromString('404://stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function canNotCreateUrlWithInvalidUser()
    {
        Url::fromString('http://mi@ss@stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function canNotCreateUrlWithInvalidPassword()
    {
        Url::fromString('http://mi:s@s@stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUrlException
     */
    public function canNotCreateUrlWithInvalidHost()
    {
        Url::fromString('http://_:80');
    }

    /**
     * @test
     */
    public function createFromEmptyStringReturnsNull()
    {
        $this->assertNull(Url::fromString(''));
    }

    /**
     * @test
     */
    public function schemeIsRecognized()
    {
        $this->assertEquals('http',
                            Url::fromString('http://stubbles.net/')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function schemeIsRecognizedForIpAddresses()
    {
        $this->assertEquals('http',
                            Url::fromString('http://127.0.0.1')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function schemeIsRecognizedIfHostIsMissing()
    {
        $this->assertEquals('file',
                            Url::fromString('file:///home')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function neverHasDefaultPort()
    {
        $this->assertFalse(Url::fromString('http://stubbles.net:80/')
                              ->hasDefaultPort()
        );
    }

    /**
     * @test
     */
    public function hasNoUserIfNoUserGiven()
    {
        $this->assertNull(Url::fromString('ftp://stubbles.net')
                             ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasDefaultUserIfNoUserGiven()
    {
        $this->assertEquals('mikey',
                            Url::fromString('ftp://stubbles.net')
                               ->getUser('mikey')
        );
    }

    /**
     * @test
     */
    public function hasGivenUser()
    {
        $this->assertEquals('mikey',
                            Url::fromString('ftp://mikey@stubbles.net')
                               ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasGivenUserEvenIfDefaultChanged()
    {
        $this->assertEquals('mikey',
                            Url::fromString('ftp://mikey@stubbles.net')
                               ->getUser('other')
        );
    }

    /**
     * @test
     */
    public function hasEmptyUser()
    {
        $this->assertEquals('',
                            Url::fromString('ftp://@stubbles.net')
                               ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasEmptyUserEvenIfDefaultChanged()
    {
        $this->assertEquals('',
                            Url::fromString('ftp://@stubbles.net')
                               ->getUser('other')
        );
    }

    /**
     * @test
     */
    public function hasNoPasswordIfNoUserGiven()
    {
        $this->assertNull(Url::fromString('ftp://stubbles.net')
                             ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasNoDefaultPasswordIfNoUserGiven()
    {
        $this->assertNull(Url::fromString('ftp://stubbles.net')
                             ->getPassword('secret')
        );
    }

    /**
     * @test
     */
    public function hasDefaultPasswordIfUserButNoPasswordGiven()
    {
        $this->assertEquals('secret',
                            Url::fromString('ftp://mikey@stubbles.net')
                               ->getPassword('secret')
        );
    }

    /**
     * @test
     */
    public function hasGivenPassword()
    {
        $this->assertEquals('secret',
                            Url::fromString('ftp://mikey:secret@stubbles.net')
                               ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasGivenPasswordEvenIfDefaultChanged()
    {
        $this->assertEquals('secret',
                            Url::fromString('ftp://mikey:secret@stubbles.net')
                               ->getPassword('other')
        );
    }

    /**
     * @test
     */
    public function hasEmptyPassword()
    {
        $this->assertEquals('',
                            Url::fromString('ftp://mikey:@stubbles.net')
                               ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasEmptyPasswordEvenIfDefaultChanged()
    {
        $this->assertEquals('',
                            Url::fromString('ftp://mikey:@stubbles.net')
                               ->getPassword('other')
        );
    }

    /**
     * @test
     */
    public function hasHostFromGivenUrl()
    {
        $this->assertEquals('stubbles.net',
                            Url::fromString('ftp://stubbles.net:21')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hostIsTransformedToLowercase()
    {
        $this->assertEquals('stubbles.net',
                            Url::fromString('ftp://stUBBles.net:21')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hasNoHostIfUrlDoesNotContainHost()
    {
        $this->assertNull(Url::fromString('file:///home')
                             ->getHost()
        );
    }

    /**
     * @test
     */
    public function getHostReturnsIpv4()
    {
        $this->assertEquals('127.0.0.1',
                            Url::fromString('http://127.0.0.1/')
                               ->getHost()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function getHostReturnsIpv6ShortNotation()
    {
        $this->assertEquals('[2001:db8:12:34::1]',
                            Url::fromString('http://[2001:db8:12:34::1]/')
                               ->getHost()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function getHostReturnsIpv6LongNotation()
    {
        $this->assertEquals('[2001:8d8f:1fe:5:abba:dbff:fefe:7755]',
                            Url::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hasNoPortIfNoPortGiven()
    {
        $this->assertNull(Url::fromString('ftp://stubbles.net')
                             ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasDefaultValueIfNoPortGiven()
    {
        $this->assertEquals(303,
                            Url::fromString('ftp://stubbles.net')
                               ->getPort(303)
        );
    }

    /**
     * @test
     */
    public function hasGivenPortIfPortGiven()
    {
        $this->assertEquals(21,
                            Url::fromString('ftp://stubbles.net:21')
                               ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasGivenPortFromIpv4Adress()
    {
        $this->assertEquals(21,
                            Url::fromString('ftp://127.0.01:21')
                               ->getPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasGivenPortFromIpv6AdressShortNotation()
    {
        $this->assertEquals(21,
                            Url::fromString('ftp://[2001:db8:12:34::1]:21')
                               ->getPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasGivenPortFromIpv6AdressLongNotation()
    {
        $this->assertEquals(21,
                            Url::fromString('ftp://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:21')
                               ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasGivenPortEvenIfDefaultChanged()
    {
        $this->assertEquals(21,
                            Url::fromString('ftp://stubbles.net:21')
                               ->getPort(303)
        );
    }

    /**
     * @test
     */
    public function getPathReturnsNullIfNoPathInGivenUrl()
    {
        $this->assertNull(Url::fromString('http://stubbles.net')
                             ->getPath()
        );
    }

    /**
     * @test
     */
    public function getPathReturnsGivenPath()
    {
        $this->assertEquals('/index.php',
                            Url::fromString('http://stubbles.net/index.php?foo=bar#baz')
                               ->getPath()
        );
    }

    /**
     * @test
     */
    public function getPathReturnsPathEvenIfNoHostPresent()
    {
        $this->assertEquals('/home',
                            Url::fromString('file:///home')
                               ->getPath()
        );
    }

    /**
     * @test
     */
    public function hasNoQueryStringIfNoneInOriginalUrl()
    {
        $this->assertFalse(Url::fromString('http://stubbles.net:80/')
                              ->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasQueryStringIfInOriginalUrl()
    {
        $this->assertTrue(Url::fromString('http://stubbles.net:80/?foo=bar')
                             ->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasNoDnsRecordWitoutHost()
    {
        $this->assertFalse(Url::fromString('file:///home/test.txt')
                              ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function returnsFalseIfHostHasNoDnsRecord()
    {
        $this->assertFalse(Url::fromString('http://example.dev/')
                              ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForExistingDomain()
    {
        $this->assertTrue(Url::fromString('http://stubbles.net/')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForLocalhost()
    {
        $this->assertTrue(Url::fromString('http://localhost')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForIpv4Localhost()
    {
        $this->assertTrue(Url::fromString('http://127.0.0.1')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasDnsRecordForIpv6Localhost()
    {
        $this->assertTrue(Url::fromString('http://[::1]')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUrl()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top',
                            Url::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlButWithoutPort()
    {
        $this->assertEquals('http://stubbles.net/index.php?content=features#top',
                            Url::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUrlWithPort()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top',
                            Url::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUrlWithoutPort()
    {
        $this->assertEquals('http://stubbles.net/index.php?content=features#top',
                            Url::fromString('http://stubbles.net/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUrlWithUsernameAndPassword()
    {
        $this->assertEquals('http://mikey:secret@stubbles.net:80/index.php?content=features#top',
                            Url::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlWithUsernameAndPasswordWithoutPort()
    {
        $this->assertEquals('http://mikey:secret@stubbles.net/index.php?content=features#top',
                            Url::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUrlWithUsername()
    {
        $this->assertEquals('http://mikey@stubbles.net:80/index.php?content=features#top',
                            Url::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlWithUsernameWithoutPort()
    {
        $this->assertEquals('http://mikey@stubbles.net/index.php?content=features#top',
                            Url::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUrlWithUsernameAndEmptyPassword()
    {
        $this->assertEquals('http://mikey:@stubbles.net:80/index.php?content=features#top',
                            Url::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlWithUsernameAndEmptyPasswordWithoutPort()
    {
        $this->assertEquals('http://mikey:@stubbles.net/index.php?content=features#top',
                            Url::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUrlWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1:80/index.php?content=features#top',
                            Url::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlButWithoutPortWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1/index.php?content=features#top',
                            Url::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUrlWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1:80/index.php?content=features#top',
                            Url::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUrlWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]:80/index.php?content=features#top',
                            Url::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlButWithoutPortWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]/index.php?content=features#top',
                            Url::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUrlWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]:80/index.php?content=features#top',
                            Url::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUrlWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top',
                            Url::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUrlButWithoutPortWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]/index.php?content=features#top',
                            Url::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUrlWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top',
                            Url::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function wrongParams()
    {
        Url::fromString('http://example.org/')
           ->addParam('test', new \stdClass());
    }

    /**
     * @test
     */
    public function paramWithoutValue()
    {
        $this->assertEquals('http://example.org/?wsdl',
                            Url::fromString('http://example.org/?wsdl')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsTrueIfParamPresent()
    {
        $this->assertTrue(Url::fromString('http://example.org/?wsdl')
                             ->hasParam('wsdl')
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsFalseIfParamNotPresent()
    {
        $this->assertFalse(Url::fromString('http://example.org/?wsdl')
                              ->hasParam('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsNullIfParamNotSet()
    {
        $this->assertNull(Url::fromString('http://example.org/?foo=bar')
                             ->getParam('bar')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsDefaultValueIfParamNotSet()
    {
        $this->assertEquals('baz',
                            Url::fromString('http://example.org/?foo=bar')
                               ->getParam('bar', 'baz')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsValueIfParamSet()
    {
        $this->assertEquals('bar',
                            Url::fromString('http://example.org/?foo=bar')
                               ->getParam('foo')
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParamChangesNothing()
    {
        $this->assertEquals('http://example.org/?wsdl',
                            Url::fromString('http://example.org/?wsdl')
                               ->removeParam('doesNotExist')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function removeExistingParamChangesQueryString()
    {
        $this->assertEquals('http://example.org/?wsdl',
                            Url::fromString('http://example.org/?wsdl&foo=bar')
                               ->removeParam('foo')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function addParamChangesQueryString()
    {
        $this->assertEquals('http://example.org/?wsdl&foo=bar',
                            Url::fromString('http://example.org/?wsdl')
                               ->addParam('foo', 'bar')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function fragmentIsNullIfNotInUrl()
    {
        $this->assertNull(Url::fromString('http://example.org/?wsdl')
                             ->getFragment()
        );
    }

    /**
     * @test
     */
    public function fragmentFromUrlIsReturned()
    {
        $this->assertEquals('top',
                            Url::fromString('http://example.org/?wsdl#top')
                               ->getFragment()
        );
    }

    /**
     * @test
     */
    public function parsedUrlReturnsNullIfNoSchemeInUrl()
    {
        $parsedUrl = new ParsedUrl('://example.org/?wsdl#top');
        $this->assertNull($parsedUrl->getScheme());
    }
}
?>