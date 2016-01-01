<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNotSameAs;
/**
 * Test for stubbles\peer\Uri.
 *
 * @group  peer
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithoutScheme()
    {
        Uri::fromString('stubbles.net');
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidScheme()
    {
        Uri::fromString('404://stubbles.net');
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidUser()
    {
        Uri::fromString('http://mi@ss@stubbles.net');
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function canNotCreateUriWithInvalidPassword()
    {
        Uri::fromString('http://mi:s@s@stubbles.net');
    }

    /**
     * @test
     * @expectedException  stubbles\peer\MalformedUriException
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
        assertNull(Uri::fromString(''));
    }

    /**
     * @test
     */
    public function schemeIsRecognized()
    {
        assert(Uri::fromString('http://stubbles.net/')->scheme(), equals('http'));
    }

    /**
     * @test
     */
    public function schemeIsRecognizedForIpAddresses()
    {
        assert(Uri::fromString('http://127.0.0.1')->scheme(), equals('http'));
    }

    /**
     * @test
     */
    public function schemeIsRecognizedIfHostIsMissing()
    {
        assert(Uri::fromString('file:///home')->scheme(), equals('file'));
    }

    /**
     * @test
     */
    public function neverHasDefaultPort()
    {
        assertFalse(
                Uri::fromString('http://stubbles.net:80/')->hasDefaultPort()
        );
    }

    /**
     * @test
     */
    public function hasNoUserIfNoUserGiven()
    {
        assertNull(Uri::fromString('ftp://stubbles.net')->user());
    }

    /**
     * @test
     */
    public function hasDefaultUserIfNoUserGiven()
    {
        assert(
                Uri::fromString('ftp://stubbles.net')->user('mikey'),
                equals('mikey')
        );
    }

    /**
     * @test
     */
    public function hasGivenUser()
    {
        assert(
                Uri::fromString('ftp://mikey@stubbles.net')->user(),
                equals('mikey')
        );
    }

    /**
     * @test
     */
    public function hasGivenUserEvenIfDefaultChanged()
    {
        assert(
                Uri::fromString('ftp://mikey@stubbles.net')->user('other'),
                equals('mikey')
        );
    }

    /**
     * @test
     */
    public function hasEmptyUser()
    {
        assertEmptyString(Uri::fromString('ftp://@stubbles.net')->user());
    }

    /**
     * @test
     */
    public function hasEmptyUserEvenIfDefaultChanged()
    {
        assertEmptyString(Uri::fromString('ftp://@stubbles.net')->user('other'));
    }

    /**
     * @test
     */
    public function hasNoPasswordIfNoUserGiven()
    {
        assertNull(Uri::fromString('ftp://stubbles.net')->password());
    }

    /**
     * @test
     */
    public function hasNoDefaultPasswordIfNoUserGiven()
    {
        assertNull(Uri::fromString('ftp://stubbles.net')->password('secret'));
    }

    /**
     * @test
     */
    public function hasDefaultPasswordIfUserButNoPasswordGiven()
    {
        assert(
                Uri::fromString('ftp://mikey@stubbles.net')->password('secret'),
                equals('secret')
        );
    }

    /**
     * @test
     */
    public function hasGivenPassword()
    {
        assert(
                Uri::fromString('ftp://mikey:secret@stubbles.net')->password(),
                equals('secret')
        );
    }

    /**
     * @test
     */
    public function hasGivenPasswordEvenIfDefaultChanged()
    {
        assert(
                Uri::fromString('ftp://mikey:secret@stubbles.net')->password('other'),
                equals('secret')
        );
    }

    /**
     * @test
     */
    public function hasEmptyPassword()
    {
        assertEmptyString(
                Uri::fromString('ftp://mikey:@stubbles.net')->password()
        );
    }

    /**
     * @test
     */
    public function hasEmptyPasswordEvenIfDefaultChanged()
    {
        assertEmptyString(
                Uri::fromString('ftp://mikey:@stubbles.net')->password('other')
        );
    }

    /**
     * @test
     */
    public function hasHostFromGivenUri()
    {
        assert(
                Uri::fromString('ftp://stubbles.net:21')->hostname(),
                equals('stubbles.net')
        );
    }

    /**
     * @test
     */
    public function hostIsTransformedToLowercase()
    {
        assert(
                Uri::fromString('ftp://stUBBles.net:21')->hostname(),
                equals('stubbles.net')
        );
    }

    /**
     * @test
     */
    public function hasNoHostIfUriDoesNotContainHost()
    {
        assertNull(Uri::fromString('file:///home')->hostname());
    }

    /**
     * @test
     */
    public function getHostReturnsIpv4()
    {
        assert(
                Uri::fromString('http://127.0.0.1/')->hostname(),
                equals('127.0.0.1')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function getHostReturnsIpv6ShortNotation()
    {
        assert(
                Uri::fromString('http://[2001:db8:12:34::1]/')->hostname(),
                equals('[2001:db8:12:34::1]')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function getHostReturnsIpv6LongNotation()
    {
        assert(
                Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/')
                        ->hostname(),
                equals('[2001:8d8f:1fe:5:abba:dbff:fefe:7755]')
        );
    }

    /**
     * @test
     */
    public function hasNoPortIfNoPortGiven()
    {
        assertNull(Uri::fromString('ftp://stubbles.net')->port());
    }

    /**
     * @test
     */
    public function hasDefaultValueIfNoPortGiven()
    {
        assert(Uri::fromString('ftp://stubbles.net')->port(303), equals(303));
    }

    /**
     * @test
     */
    public function hasGivenPortIfPortGiven()
    {
        assert(Uri::fromString('ftp://stubbles.net:21')->port(), equals(21));
    }

    /**
     * @test
     */
    public function hasGivenPortFromIpv4Adress()
    {
        assert(Uri::fromString('ftp://127.0.01:21')->port(), equals(21));
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasGivenPortFromIpv6AdressShortNotation()
    {
        assert(Uri::fromString('ftp://[2001:db8:12:34::1]:21')->port(), equals(21));
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasGivenPortFromIpv6AdressLongNotation()
    {
        assert(
                Uri::fromString('ftp://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:21')->port(),
                equals(21)
        );
    }

    /**
     * @test
     */
    public function hasGivenPortEvenIfDefaultChanged()
    {
        assert(Uri::fromString('ftp://stubbles.net:21')->port(303), equals(21));
    }

    /**
     * @test
     */
    public function getPathReturnsNullIfNoPathInGivenUri()
    {
        assertNull(Uri::fromString('http://stubbles.net')->path());
    }

    /**
     * @test
     */
    public function getPathReturnsGivenPath()
    {
        assert(
                Uri::fromString('http://stubbles.net/index.php?foo=bar#baz')->path(),
                equals('/index.php')
        );
    }

    /**
     * @test
     */
    public function getPathReturnsPathEvenIfNoHostPresent()
    {
        assert(Uri::fromString('file:///home')->path(), equals('/home'));
    }

    /**
     * @test
     */
    public function hasNoQueryStringIfNoneInOriginalUri()
    {
        assertFalse(
                Uri::fromString('http://stubbles.net:80/')->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasQueryStringIfInOriginalUri()
    {
        assertTrue(
                Uri::fromString('http://stubbles.net:80/?foo=bar')->hasQueryString()
        );
    }

    /**
     * @test
     */
    public function hasNoDnsRecordWitoutHost()
    {
        assertFalse(
                Uri::fromString('file:///home/test.txt')->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForLocalhost()
    {
        assertTrue(
                Uri::fromString('http://localhost')->hasDnsRecord()
        );
    }

    /**
     * @test
     */
    public function hasDnsRecordForIpv4Localhost()
    {
        assertTrue(
                Uri::fromString('http://127.0.0.1')->hasDnsRecord()
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function hasDnsRecordForIpv6Localhost()
    {
        assertTrue(
                Uri::fromString('http://[::1]')->hasDnsRecord()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canBeCastedToString()
    {
        assert(
                (string) Uri::fromString('http://stubbles.net:80/index.php?content=features#top'),
                equals('http://stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUri()
    {
        assert(
                Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                        ->asString(),
                equals('http://stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPort()
    {
        assert(
                Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://stubbles.net/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithPort()
    {
        assert(
                Uri::fromString('http://stubbles.net:80/index.php?content=features#top')
                        ->asStringWithNonDefaultPort(),
                equals('http://stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithoutPort()
    {
        assert(
                Uri::fromString('http://stubbles.net/index.php?content=features#top')
                        ->asStringWithNonDefaultPort(),
                equals('http://stubbles.net/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsernameAndPassword()
    {
        assert(
                Uri::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                        ->asString(),
                equals('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameAndPasswordWithoutPort()
    {
        assert(
                Uri::fromString('http://mikey:secret@stubbles.net:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://mikey:secret@stubbles.net/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsername()
    {
        assert(
                Uri::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                        ->asString(),
                equals('http://mikey@stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameWithoutPort()
    {
        assert(
                Uri::fromString('http://mikey@stubbles.net:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://mikey@stubbles.net/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithUsernameAndEmptyPassword()
    {
        assert(
                Uri::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                        ->asString(),
                equals('http://mikey:@stubbles.net:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriWithUsernameAndEmptyPasswordWithoutPort()
    {
        assert(
                Uri::fromString('http://mikey:@stubbles.net:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://mikey:@stubbles.net/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringReturnsOriginalGivenUriWithIpv4Address()
    {
        assert(
                Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                        ->asString(),
                equals('http://127.0.0.1:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv4Address()
    {
        assert(
                Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://127.0.0.1/index.php?content=features#top')
        );
    }

    /**
     * @test
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv4Address()
    {
        assert(
                Uri::fromString('http://127.0.0.1:80/index.php?content=features#top')
                        ->asStringWithNonDefaultPort(),
                equals('http://127.0.0.1:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUriWithIpv6AddressShortNotation()
    {
        assert(
                Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                        ->asString(),
                equals('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv6AddressShortNotation()
    {
        assert(
                Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://[2001:db8:12:34::1]/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv6AddressShortNotation()
    {
        assert(
                Uri::fromString('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
                        ->asStringWithNonDefaultPort(),
                equals('http://[2001:db8:12:34::1]:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringReturnsOriginalGivenUriWithIpv6AddressLongNotation()
    {
        assert(
                Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                        ->asString(),
                equals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithoutPortReturnsOriginalGivenUriButWithoutPortWithIpv6AddressLongNotation()
    {
        assert(
                Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                        ->asStringWithoutPort(),
                equals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @group  bug258
     */
    public function asStringWithNonDefaultPortReturnsOriginalGivenUriWithIpv6AddressLongNotation()
    {
        assert(
                Uri::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
                        ->asStringWithNonDefaultPort(),
                equals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/index.php?content=features#top')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function wrongParams()
    {
        Uri::fromString('http://example.org/')->addParam('test', new \stdClass());
    }

    /**
     * @test
     */
    public function paramWithoutValue()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl')->asStringWithoutPort(),
                equals('http://example.org/?wsdl')
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsTrueIfParamPresent()
    {
        assertTrue(
                Uri::fromString('http://example.org/?wsdl')->hasParam('wsdl')
        );
    }

    /**
     * @test
     */
    public function hasParamReturnsFalseIfParamNotPresent()
    {
        assertFalse(
                Uri::fromString('http://example.org/?wsdl')->hasParam('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsNullIfParamNotSet()
    {
        assertNull(
                Uri::fromString('http://example.org/?foo=bar')->param('bar')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsDefaultValueIfParamNotSet()
    {
        assert(
                Uri::fromString('http://example.org/?foo=bar')->param('bar', 'baz'),
                equals('baz')
        );
    }

    /**
     * @test
     */
    public function getParamReturnsValueIfParamSet()
    {
        assert(
                Uri::fromString('http://example.org/?foo=bar')->param('foo'),
                equals('bar')
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParamChangesNothing()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl')
                        ->removeParam('doesNotExist')
                        ->asStringWithoutPort(),
                equals('http://example.org/?wsdl')
        );
    }

    /**
     * @test
     */
    public function removeExistingParamChangesQueryString()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl&foo=bar')
                        ->removeParam('foo')
                        ->asStringWithoutPort(),
                equals('http://example.org/?wsdl')
        );
    }

    /**
     * @test
     * @since  5.1.2
     */
    public function addParamsChangesQueryString()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl')
                        ->addParams(['foo' => 'bar', 'baz' => '303'])
                        ->asStringWithoutPort(),
                equals('http://example.org/?wsdl&foo=bar&baz=303')
        );
    }

    /**
     * @test
     */
    public function addParamChangesQueryString()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl')
                        ->addParam('foo', 'bar')
                        ->asStringWithoutPort(),
                equals('http://example.org/?wsdl&foo=bar')
        );
    }

    /**
     * @test
     */
    public function fragmentIsNullIfNotInUri()
    {
        assertNull(Uri::fromString('http://example.org/?wsdl')->fragment());
    }

    /**
     * @test
     */
    public function fragmentFromUriIsReturned()
    {
        assert(
                Uri::fromString('http://example.org/?wsdl#top')->fragment(),
                equals('top')
        );
    }

    /**
     * @test
     */
    public function parsedUriReturnsNullIfNoSchemeInUri()
    {
        $parsedUri = new ParsedUri('://example.org/?wsdl#top');
        assertNull($parsedUri->scheme());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function emptySchemeEqualsNull()
    {
        $parsedUri = new ParsedUri('://example.org/?wsdl#top');
        assertTrue($parsedUri->schemeEquals(null));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function emptySchemeDoesNotEqualEmptyString()
    {
        $parsedUri = new ParsedUri('://example.org/?wsdl#top');
        assertFalse($parsedUri->schemeEquals(''));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function schemeEqualsOnlyOriginalScheme()
    {
        $parsedUri = new ParsedUri('foo://example.org/?wsdl#top');
        assertFalse($parsedUri->schemeEquals('bar'));
        assertTrue($parsedUri->schemeEquals('foo'));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function emptyPortEqualsNull()
    {
        $parsedUri = new ParsedUri('foo://example.org/?wsdl#top');
        assertTrue($parsedUri->portEquals(null));
        assertFalse($parsedUri->portEquals(''));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function emptyPortDoesNotEqualEmptyString()
    {
        $parsedUri = new ParsedUri('foo://example.org/?wsdl#top');
        assertFalse($parsedUri->portEquals(''));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function portEqualsOnlyOriginalPort()
    {
        $parsedUri = new ParsedUri('foo://example.org:77/?wsdl#top');
        assertTrue($parsedUri->portEquals(77));
        assertFalse($parsedUri->portEquals(80));
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function hasNoQueryStringIfNoneGiven()
    {
        assertFalse(
                Uri::fromString('http://example.org/foo')->hasQueryString()
        );
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function hasQueryStringIfGiven()
    {
        assertTrue(
                Uri::fromString('http://example.org/?foo=bar&baz=true')->hasQueryString()
        );
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function hasQueryStringIfParamAdded()
    {
        assertTrue(
                Uri::fromString('http://example.org/')
                   ->addParam('foo', 'bar')
                   ->hasQueryString()
        );
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function queryStringIsEmptyIfNoneGiven()
    {
        assertNull(
                Uri::fromString('http://example.org/foo')->queryString()
        );
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function queryStringEqualsGivenQueryString()
    {
        assert(
                Uri::fromString('http://example.org/?foo=bar&baz=true')
                        ->queryString(),
                equals('foo=bar&baz=true')
        );
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function queryStringEqualsAddedParameters()
    {
        assert(
                Uri::fromString('http://example.org/')
                        ->addParam('foo', 'bar')
                        ->queryString(),
                equals('foo=bar')
        );
    }

    /**
     * @since  5.0.1
     * @test
     * @group  issue_119
     * @expectedException  stubbles\peer\MalformedUriException
     */
    public function illegalArgumentExceptionFromUnbalancedQueryStringTurnedIntoMalformedUriException()
    {
        Uri::fromString('http://example.org/?foo[bar=300&baz=200');
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function withPathExchangesPathCompletely()
    {
        assert(
                Uri::fromString('http://example.org/foo')->withPath('/bar'),
                equals('http://example.org/bar')
        );
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function withPathReturnsNewInstance()
    {
        $uri = Uri::fromString('http://example.org/foo');
        assert($uri->withPath('/bar'), isNotSameAs($uri));
    }
}
