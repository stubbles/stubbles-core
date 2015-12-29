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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isFalse;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isTrue;
/**
 * Test for stubbles\peer\http\HttpVersion.
 *
 * @since  4.0.0
 * @group  peer
 * @group  peer_http
 */
class HttpVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function emptyVersions()
    {
        return [[''], [null]];
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given HTTP version is empty
     * @dataProvider  emptyVersions
     */
    public function parseFromStringThrowsIllegalArgumentExceptionWhenGivenVersionIsEmpty($empty)
    {
        HttpVersion::fromString($empty);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given HTTP version "invalid" can not be parsed
     */
    public function parseFromStringThrowsIllegalArgumentExceptionWhenParsingFails()
    {
        HttpVersion::fromString('invalid');
    }

    /**
     * @test
     */
    public function fromStringDetectsCorrectMajorVersion()
    {
        assert(HttpVersion::fromString('HTTP/1.2')->major(), equals(1));
    }

    /**
     * @test
     */
    public function fromStringDetectsCorrectMinorVersion()
    {
        assert(HttpVersion::fromString('HTTP/1.2')->minor(), equals(2));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given major version "foo" is not an integer
     */
    public function constructWithInvalidMajorArgumentThrowsIllegalArgumentException()
    {
        new HttpVersion('foo', 1);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given minor version "foo" is not an integer
     */
    public function constructWithInvalidMinorArgumentThrowsIllegalArgumentException()
    {
        new HttpVersion(1, 'foo');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Major version can not be negative
     */
    public function constructWithNegativeMajorVersionThrowsIllegalArgumentException()
    {
        new HttpVersion(-2, 1);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Major version can not be negative
     */
    public function parseFromStringWithNegativeMajorNumberThrowsIllegalArgumentExceptionWhenParsingFails()
    {
        HttpVersion::fromString('HTTP/-2.1');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Minor version can not be negative
     */
    public function constructWithNegativeMinorVersionThrowsIllegalArgumentException()
    {
        new HttpVersion(1, -2);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Minor version can not be negative
     */
    public function parseFromStringWithNegativeMinorNumberThrowsIllegalArgumentExceptionWhenParsingFails()
    {
        HttpVersion::fromString('HTTP/2.-1');
    }

    /**
     * @test
     */
    public function castToStringReturnsCorrectVersionString()
    {
        $versionString = 'HTTP/1.1';
        assert(
                (string) HttpVersion::fromString($versionString),
                equals($versionString)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given HTTP version is empty
     * @dataProvider  emptyVersions
     */
    public function castFromEmptyWithoutDefaultThrowsIllegalArgumentException($empty)
    {
        HttpVersion::castFrom($empty);
    }

    /**
     * @test
     */
    public function castFromInstanceReturnsInstance()
    {
        $httpVersion = new HttpVersion(1, 1);
        assert(HttpVersion::castFrom($httpVersion), isSameAs($httpVersion));
    }

    /**
     * @test
     */
    public function castFromStringReturnsInstance()
    {
        assert(HttpVersion::castFrom('HTTP/1.1'), equals(new HttpVersion(1, 1)));
    }

    /**
     * @test
     * @dataProvider  emptyVersions
     */
    public function doesNotEqualEmptyVersion($empty)
    {
        assert(
                HttpVersion::fromString(HttpVersion::HTTP_1_1)
                        ->equals($empty),
                isFalse()
        );
    }

    /**
     * @test
     */
    public function doesNotEqualInvalidVersion()
    {
        assert(
                HttpVersion::fromString(HttpVersion::HTTP_1_1)
                        ->equals('HTTP/404'),
                isFalse()
        );
    }

    /**
     * @test
     */
    public function doesNotEqualWhenMajorVersionDiffers()
    {
        assert(
                HttpVersion::fromString(HttpVersion::HTTP_1_1)
                        ->equals('HTTP/2.0'),
                isFalse()
        );
    }

    /**
     * @test
     */
    public function doesNotEqualWhenMinorVersionDiffers()
    {
        assert(
                HttpVersion::fromString(HttpVersion::HTTP_1_1)
                        ->equals(HttpVersion::HTTP_1_0),
                isFalse()
        );
    }

    /**
     * @test
     */
    public function isEqualWhenMajorAndMinorVersionEqual()
    {
        assert(
                HttpVersion::fromString(HttpVersion::HTTP_1_1)->equals(HttpVersion::HTTP_1_1),
                isTrue()
        );
    }
}
