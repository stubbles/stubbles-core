<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Predicate to test that something is an IPv4 address.
 *
 * @api
 * @since  4.0.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class IsIpV4Address extends Predicate
{
    use ReusablePredicate;

    /**
     * test that the given value is an IPv4 address
     *
     * @param   mixed  $value
     * @return  bool   true if value is equal to expected value, else false
     */
    public function test($value)
    {
        return (bool) preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $value);
    }
}
