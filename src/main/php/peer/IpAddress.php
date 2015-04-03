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
use stubbles\predicate\IsIpAddress;
/**
 * Represents an ip address and possible operations on an ip address.
 *
 * @since  4.0.0
 */
class IpAddress
{
    /**
     * actual ip address
     *
     * @type  string
     */
    private $ip;

    /**
     * constructor
     *
     * Integer values are considered to be representations of an IP address as
     * long.
     *
     * The given value will be checked with \stubbles\predicate\IsIpAddress. If
     * the predicate returns false an IllegalArgumentException will be thrown.
     *
     * @param   int|string  $ip
     * @throws  \InvalidArgumentException
     */
    public function __construct($ip)
    {
        if (is_int($ip)) {
            $this->ip = long2ip($ip);
        } else {
            $this->ip = $ip;
        }

        if (!IsIpAddress::instance()->test($this->ip)) {
            throw new \InvalidArgumentException(
                    'Given ip address ' . $this->ip
                    . ' does not denote a valid IP address'
            );
        }
    }

    /**
     * casts given value to ip address
     *
     * @param   int|string|\stubbles\peer\IpAddress $ip
     * @return  \stubbles\peer\IpAddress
     */
    public static function castFrom($ip)
    {
        if ($ip instanceof self) {
            return $ip;
        }

        return new self($ip);
    }

    /**
     * checks if IP address is in given CIDR range
     *
     * A cidr range is commonly notated as 10.16/13. From this, $cidrIpShort
     * would be 10.16 and $cidrMask would be 13 or 47.
     *
     * Please note that this method currently supports IPv4 only.
     *
     * @param   string  $cidrIpShort
     * @param   string  $cidrMask
     * @return  bool
     * @see     http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing#CIDR_notation
     */
    public function isInCidrRange($cidrIpShort, $cidrMask)
    {
        list($lower, $upper) = $this->calculateIpRange($this->completeCidrIp($cidrIpShort), $cidrMask);
        return $this->asLong() >= $lower &&  $this->asLong() <= $upper;
    }

    /**
     * returns lower and upper ip for IP range as long
     *
     * @param   string  $cidrIpLong
     * @param   string  $cidrMask
     * @return  int[]
     */
    private function calculateIpRange($cidrIpLong, $cidrMask)
    {
        $netWork = $cidrIpLong & $this->netMask($cidrMask);
        $lower   = $netWork + 1; // ignore network ID (eg: 192.168.1.0)
        $upper   = ($netWork | $this->inverseNetMask($cidrMask)) - 1 ; //  ignore broadcast IP (eg: 192.168.1.255)
        return array($lower, $upper);
    }

    /**
     * turns short version of a CIDR IP address into its complete version
     *
     * @param   string  $cidrIpShort
     * @return  int
     */
    private function completeCidrIp($cidrIpShort)
    {
        return ip2long($cidrIpShort . str_repeat('.0', 3 - substr_count($cidrIpShort, '.')));
    }

    /**
     * calculates net mask from cidr mask
     *
     * @param   string  $cidrMask
     * @return  int
     */
    private function netMask($cidrMask)
    {
        return bindec(str_repeat('1', $cidrMask) . str_repeat('0', 32 - $cidrMask));
    }

    /**
     * calculates inverse net mask from cidr mask
     *
     * @param   string  $cidrMask
     * @return  int
     */
    private function inverseNetMask($cidrMask)
    {
        return bindec(str_repeat('0', $cidrMask) . str_repeat('1',  32 - $cidrMask));
    }

    /**
     * returns ip address as long
     *
     * @return  int
     */
    public function asLong()
    {
        return ip2long($this->ip);
    }

    /**
     * returns string representation
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->ip;
    }

    /**
     * opens socket to this ip address
     *
     * @param   int  $port     port to connect to
     * @param   int  $timeout  connection timeout
     * @return  \stubbles\peer\Socket
     */
    public function openSocket($port, $timeout = 5)
    {
        return new Socket($this->ip, $port, null, $timeout);
    }

    /**
     * opens secure socket using ssl to this ip address
     *
     * @param   int  $port     port to connect to
     * @param   int  $timeout  connection timeout
     * @return  \stubbles\peer\Socket
     */
    public function openSecureSocket($port, $timeout = 5)
    {
        return new Socket($this->ip, $port, 'ssl://', $timeout);
    }
}
