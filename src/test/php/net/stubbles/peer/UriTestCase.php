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
 * Test for net\stubbles\peer\Uri.
 *
 * @group  peer
 */
class UriTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithoutScheme()
    {
        Uri::fromString('stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidScheme()
    {
        Uri::fromString('404://stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidUser()
    {
        Uri::fromString('http://mi@ss@stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidPassword()
    {
        Uri::fromString('http://mi:s@s@stubbles.net');
    }

    /**
     * @test
     * @expectedException  net\stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidHost()
    {
        Uri::fromString('http://_:80');
    }

    /**
     * @test
     */
    public function createFromEmptyStringReturnsNull()
    {
        $this->assertNull(Uri::fromString(''));
    }

    /**
     * @test
     */
    public function schemeIsRecognized()
    {
        $this->assertEquals('http',
                            Uri::fromString('http://stubbles.net/')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function schemeIsRecognizedForIpAddresses()
    {
        $this->assertEquals('http',
                            Uri::fromString('http://127.0.0.1')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function schemeIsRecognizedIfHostIsMissing()
    {
        $this->assertEquals('file',
                            Uri::fromString('file:///home')
                               ->getScheme()
        );
    }

    /**
     * @test
     */
    public function neverHasDefaultPort()
    {
        $this->assertFalse(Uri::fromString('http://stubbles.net:80/')
                              ->hasDefaultPort()
        );
    }

    /**
     * @test
     */
    public function hasNoUserIfNoUserGiven()
    {
        $this->assertNull(Uri::fromString('ftp://stubbles.net')
                             ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasDefaultUserIfNoUserGiven()
    {
        $this->assertEquals('mikey',
                            Uri::fromString('ftp://stubbles.net')
                               ->getUser('mikey')
        );
    }

    /**
     * @test
     */
    public function hasGivenUser()
    {
        $this->assertEquals('mikey',
                            Uri::fromString('ftp://mikey@stubbles.net')
                               ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasGivenUserEvenIfDefaultChanged()
    {
        $this->assertEquals('mikey',
                            Uri::fromString('ftp://mikey@stubbles.net')
                               ->getUser('other')
        );
    }

    /**
     * @test
     */
    public function hasEmptyUser()
    {
        $this->assertEquals('',
                            Uri::fromString('ftp://@stubbles.net')
                               ->getUser()
        );
    }

    /**
     * @test
     */
    public function hasEmptyUserEvenIfDefaultChanged()
    {
        $this->assertEquals('',
                            Uri::fromString('ftp://@stubbles.net')
                               ->getUser('other')
        );
    }

    /**
     * @test
     */
    public function hasNoPasswordIfNoUserGiven()
    {
        $this->assertNull(Uri::fromString('ftp://stubbles.net')
                             ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasNoDefaultPasswordIfNoUserGiven()
    {
        $this->assertNull(Uri::fromString('ftp://stubbles.net')
                             ->getPassword('secret')
        );
    }

    /**
     * @test
     */
    public function hasDefaultPasswordIfUserButNoPasswordGiven()
    {
        $this->assertEquals('secret',
                            Uri::fromString('ftp://mikey@stubbles.net')
                               ->getPassword('secret')
        );
    }

    /**
     * @test
     */
    public function hasGivenPassword()
    {
        $this->assertEquals('secret',
                            Uri::fromString('ftp://mikey:secret@stubbles.net')
                               ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasGivenPasswordEvenIfDefaultChanged()
    {
        $this->assertEquals('secret',
                            Uri::fromString('ftp://mikey:secret@stubbles.net')
                               ->getPassword('other')
        );
    }

    /**
     * @test
     */
    public function hasEmptyPassword()
    {
        $this->assertEquals('',
                            Uri::fromString('ftp://mikey:@stubbles.net')
                               ->getPassword()
        );
    }

    /**
     * @test
     */
    public function hasEmptyPasswordEvenIfDefaultChanged()
    {
        $this->assertEquals('',
                            Uri::fromString('ftp://mikey:@stubbles.net')
                               ->getPassword('other')
        );
    }

    /**
     * @test
     */
    public function hasHostFromGivenUri()
    {
        $this->assertEquals('stubbles.net',
                            Uri::fromString('ftp://stubbles.net:21')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hostIsTransformedToLowercase()
    {
        $this->assertEquals('stubbles.net',
                            Uri::fromString('ftp://stUBBles.net:21')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hasNoHostIfUriDoesNotContainHost()
    {
        $this->assertNull(Uri::fromString('file:///home')
                             ->getHost()
        );
    }

    /**
     * @test
     */
    public function getHostReturnsIpv4()
    {
        $this->assertEquals('127.0.0.1',
                            Uri::fromString('http://127.0.0.1/')
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
                            Uri::fromString('http://[2001:db8:12:34::1]/')
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
                            Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/')
                               ->getHost()
        );
    }

    /**
     * @test
     */
    public function hasNoPortIfNoPortGiven()
    {
        $this->assertNull(Uri::fromString('ftp://stubbles.net')
                             ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasDefaultValueIfNoPortGiven()
    {
        $this->assertEquals(303,
                            Uri::fromString('ftp://stubbles.net')
                               ->getPort(303)
        );
    }

    /**
     * @test
     */
    public function hasGivenPortIfPortGiven()
    {
        $this->assertEquals(21,
                            Uri::fromString('ftp://stubbles.net:21')
                               ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasGivenPortFromIpv4Adress()
    {
        $this->assertEquals(21,
                            Uri::fromString('ftp://127.0.01:21')
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
                            Uri::fromString('ftp://[2001:db8:12:34::1]:21')
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
                            Uri::fromString('ftp://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:21')
                               ->getPort()
        );
    }

    /**
     * @test
     */
    public function hasGivenPortEvenIfDefaultChanged()
    {
        $this->assertEquals(21,
                            Uri::fromString('ftp://stubbles.net:21')
                               ->getPort(303)
        );
    }

    /**
     * @test
     */
    public function getPathReturnsNullIfNoPathInGivenUri()
    {
        $this->assertNull(Uri::fromString('http://stubbles.net')
                             ->getPath()
        );
    }

    /**
     * @test
     */
    public function getPathReturnsGivenPath()
    {
        $this->assertEquals('/index.php',
                            Uri::fromString('http://stubbles.net/index.php?foo=bar#baz')
                               ->getPath()
        );
    }

    /**
     * @test
     */
    public function getPathReturnsPathEvenIfNoHostPresent()
    {
        $this->assertEquals('/home',
                            Uri::fromString('file:///home')
                               ->getPath()
        );
    }

    /**
     * @test
     */
    public function hasNoQueryStringIfNoneInOriginalUri()
    {
        $this->assertFalse(Uri::fromString('http://stubbles.net:80/')
                              ->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasQueryStringIfInOriginalUri()
    {
        $this->assertTrue(Uri::fromString('http://stubbles.net:80/?foo=bar')
                             ->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasNoDnsRecordWitoutHost()
    {
        $this->assertFalse(Uri::fromString('file:///home/test.txt')
                              ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function returnsFalseIfHostHasNoDnsRecord()
    {
        $this->assertFalse(Uri::fromString('http://example.dev/')
                              ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForExistingDomain()
    {
        $this->assertTrue(Uri::fromString('http://stubbles.net/')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForLocalhost()
    {
        $this->assertTrue(Uri::fromString('http://localhost')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForIpv4Localhost()
    {
        $this->assertTrue(Uri::fromString('http://127.0.0.1')
                             ->hasDnsRecord()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasDnsRecordForIpv6Localhost()
    {
        $this->assertTrue(Uri::fromString('http://[::1]')
                             ->hasDnsRecord()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeCastedToString()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top',
                            (string) Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUri()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top',
                            Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPort()
    {
        $this->assertEquals('http://stubbles.net/index.php?content=features#top',
                            Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithPort()
    {
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top',
                            Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithoutPort()
    {
        $this->assertEquals('http://stubbles.net/index.php?content=features#top',
                            Uri::fromString('http://stubbles.net/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsernameAndPassword()
    {
        $this->assertEquals('http://mikey:secret@stubbles.net:80/index.php?content=features#top',
                            Uri::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameAndPasswordWithoutPort()
    {
        $this->assertEquals('http://mikey:secret@stubbles.net/index.php?content=features#top',
                            Uri::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsername()
    {
        $this->assertEquals('http://mikey@stubbles.net:80/index.php?content=features#top',
                            Uri::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameWithoutPort()
    {
        $this->assertEquals('http://mikey@stubbles.net/index.php?content=features#top',
                            Uri::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsernameAndEmptyPassword()
    {
        $this->assertEquals('http://mikey:@stubbles.net:80/index.php?content=features#top',
                            Uri::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameAndEmptyPasswordWithoutPort()
    {
        $this->assertEquals('http://mikey:@stubbles.net/index.php?content=features#top',
                            Uri::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1:80/index.php?content=features#top',
                            Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1/index.php?content=features#top',
                            Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv4Address()
    {
        $this->assertEquals('http://127.0.0.1:80/index.php?content=features#top',
                            Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUriWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]:80/index.php?content=features#top',
                            Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]/index.php?content=features#top',
                            Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv6AddressShortNotation()
    {
        $this->assertEquals('http://[2001:db8:12:34::1]:80/index.php?content=features#top',
                            Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUriWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top',
                            Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asString()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]/index.php?content=features#top',
                            Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv6AddressLongNotation()
    {
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top',
                            Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                               ->asStringWithNonDefaultPort()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function wrongParams()
    {
        Uri::fromString('http://example.org/')
           ->addParam('test', new \stdClass());
    }

    /**
     * @test
     */
    public function paramWithoutValue()
    {
        $this->assertEquals('http://example.org/?wsdl',
                            Uri::fromString('http://example.org/?wsdl')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsTrueIfParamPresent()
    {
        $this->assertTrue(Uri::fromString('http://example.org/?wsdl')
                             ->hasParam('wsdl')
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsFalseIfParamNotPresent()
    {
        $this->assertFalse(Uri::fromString('http://example.org/?wsdl')
                              ->hasParam('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsNullIfParamNotSet()
    {
        $this->assertNull(Uri::fromString('http://example.org/?foo=bar')
                             ->getParam('bar')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsDefaultValueIfParamNotSet()
    {
        $this->assertEquals('baz',
                            Uri::fromString('http://example.org/?foo=bar')
                               ->getParam('bar', 'baz')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsValueIfParamSet()
    {
        $this->assertEquals('bar',
                            Uri::fromString('http://example.org/?foo=bar')
                               ->getParam('foo')
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParamChangesNothing()
    {
        $this->assertEquals('http://example.org/?wsdl',
                            Uri::fromString('http://example.org/?wsdl')
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
                            Uri::fromString('http://example.org/?wsdl&foo=bar')
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
                            Uri::fromString('http://example.org/?wsdl')
                               ->addParam('foo', 'bar')
                               ->asStringWithoutPort()
        );
    }

    /**
     * @test
     */
    public function fragmentIsNullIfNotInUri()
    {
        $this->assertNull(Uri::fromString('http://example.org/?wsdl')
                             ->getFragment()
        );
    }

    /**
     * @test
     */
    public function fragmentFromUriIsReturned()
    {
        $this->assertEquals('top',
                            Uri::fromString('http://example.org/?wsdl#top')
                               ->getFragment()
        );
    }

    /**
     * @test
     */
    public function parsedUriReturnsNullIfNoSchemeInUri()
    {
        $parsedUri = new ParsedUri('://example.org/?wsdl#top');
        $this->assertNull($parsedUri->getScheme());
    }
}
?>