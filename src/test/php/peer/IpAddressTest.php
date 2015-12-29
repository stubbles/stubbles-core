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
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\peer\IpAddress.
 *
 * @group  peer
 * @since  4.0.0
 */
class IpAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [['foo'], [-1.5], [true], [false]];
    }

    /**
     * @test
     * @dataProvider  invalidValues
     * @expectedException  InvalidArgumentException
     */
    public function constructWithInvalidValueThrowsIllegalArgumentException($invalidValue)
    {
        new IpAddress($invalidValue);
    }

    /**
     * @test
     */
    public function createWithLong()
    {
        assert(new IpAddress(2130706433), equals('127.0.0.1'));
    }

    /**
     * @return  array
     */
    public function validValues()
    {
        return [[2130706433], ['127.0.0.1']];
    }

    /**
     * @test
     * @dataProvider  validValues
     */
    public function castFromCreatesIpAddress($value)
    {
        assert(IpAddress::castFrom($value), equals('127.0.0.1'));
    }

    /**
     * @test
     */
    public function castFromInstanceReturnsInstance()
    {
        $ipAddress = new IpAddress('127.0.0.1');
        assert(IpAddress::castFrom($ipAddress), isSameAs($ipAddress));
    }

    /**
     * @test
     */
    public function asLongReturnsLongValueForIpAddress()
    {
        assert(IpAddress::castFrom('127.0.0.1')->asLong(), equals(2130706433));
    }

    /**
     * @test
     */
    public function createSocketReturnsSocketInstance()
    {
        assert(
                IpAddress::castFrom('127.0.0.1')->createSocket(80),
                isInstanceOf(Socket::class)
        );
    }

    /**
     * @test
     */
    public function createSecureSocketReturnsSocketInstance()
    {
        assert(
                IpAddress::castFrom('127.0.0.1')->createSecureSocket(443),
                isInstanceOf(Socket::class)
        );
    }

    /**
     * @test
     */
    public function openSecureSocketUsesSsl()
    {
        assertTrue(
                IpAddress::castFrom('127.0.0.1')
                        ->createSecureSocket(443)
                        ->usesSsl()
        );
    }

    /**
     * @return  array
     */
    public function containedInCidr()
    {
        return [['10.16.0.1', '10.16', '13'],
                ['10.23.255.253', '10.16', '13'],
                ['10.23.255.254', '10.16', '13'],
                ['172.19.13.1', '172.19.13', '24'],
                ['172.19.13.2', '172.19.13', '24'],
                ['172.19.13.253', '172.19.13', '24'],
                ['172.19.13.254', '172.19.13', '24'],
                ['217.160.127.241', '217.160.127.240', '28'],
                ['217.160.127.242', '217.160.127.240', '28'],
                ['217.160.127.253', '217.160.127.240', '28'],
                ['217.160.127.254', '217.160.127.240', '28']
        ];
    }

    /**
     * @param  string  $ip
     * @param  string  $cidrIpShort
     * @param  string  $cidrMask
     * @test
     * @dataProvider  containedInCidr
     */
    public function isInCidrRangeReturnsTrueIfIpIsInRange($ip, $cidrIpShort, $cidrMask)
    {
        assertTrue(
                IpAddress::castFrom($ip)->isInCidrRange($cidrIpShort, $cidrMask)
        );
    }

    /**
     * @return  array
     */
    public function notContainedInCidr()
    {
        return [['10.15.0.1', '10.16', '13'],
                ['10.24.0.1', '10.16', '13'],
                ['172.19.12.254', '172.19.13', '24'],
                ['172.19.14.1', '172.19.13', '24'],
                ['217.160.127.238', '217.160.127.240', '28'],
                ['217.160.127.239', '217.160.127.240', '28'],
                ['217.160.128.1', '217.160.127.240', '28']
        ];
    }

    /**
     * @param  string  $ip
     * @param  string  $cidrIpShort
     * @param  string  $cidrMask
     * @test
     * @dataProvider  notContainedInCidr
     */
    public function isInCidrRangeReturnsFalseIfIpIsNotInRange($ip, $cidrIpShort, $cidrMask)
    {
        assertFalse(
                IpAddress::castFrom($ip)->isInCidrRange($cidrIpShort, $cidrMask)
        );
    }
}
