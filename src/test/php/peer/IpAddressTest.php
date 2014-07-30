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
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals('127.0.0.1', new IpAddress(2130706433));
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
        $this->assertEquals('127.0.0.1', IpAddress::castFrom($value));
    }

    /**
     * @test
     */
    public function castFromInstanceReturnsInstance()
    {
        $ipAddress = new IpAddress('127.0.0.1');
        $this->assertSame($ipAddress, IpAddress::castFrom($ipAddress));
    }

    /**
     * @test
     */
    public function asLongReturnsLongValueForIpAddress()
    {
        $this->assertEquals(2130706433, IpAddress::castFrom('127.0.0.1')->asLong());
    }

    /**
     * @test
     */
    public function openSocketReturnsSocketInstance()
    {
        $this->assertInstanceOf(
                'stubbles\peer\Socket',
                IpAddress::castFrom('127.0.0.1')->openSocket(80)
        );
    }

    /**
     * @test
     */
    public function openSocketIsNotConnected()
    {
        $this->assertFalse(
                IpAddress::castFrom('127.0.0.1')->openSocket(80)->isConnected()
        );
    }

    /**
     * @test
     */
    public function openSecureSocketReturnsSocketInstance()
    {
        $this->assertInstanceOf(
                'stubbles\peer\Socket',
                IpAddress::castFrom('127.0.0.1')->openSecureSocket(443)
        );
    }

    /**
     * @test
     */
    public function openSecureSocketIsNotConnected()
    {
        $this->assertFalse(
                IpAddress::castFrom('127.0.0.1')->openSecureSocket(443)->isConnected()
        );
    }

    /**
     * @test
     */
    public function openSecureSocketUsesSsl()
    {
        $this->assertTrue(
                IpAddress::castFrom('127.0.0.1')->openSecureSocket(443)->usesSsl()
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
        $this->assertTrue(IpAddress::castFrom($ip)->isInCidrRange($cidrIpShort, $cidrMask));
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
        $this->assertFalse(IpAddress::castFrom($ip)->isInCidrRange($cidrIpShort, $cidrMask));
    }
}
