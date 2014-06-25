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
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Predicate to test that something is equal.
 *
 * This class can compare any scalar value with an expected value. The
 * value to test has to be of the same type and should have the same
 * content as the expected value.
 *
 * @api
 * @since  4.0.0
 */
class Equals extends Predicate
{
    /**
     * the expected password
     *
     * @type  string
     */
    private $expected = null;

    /**
     * constructor
     *
     * @param   scalar|null  $expected
     * @throws  IllegalArgumentException
     */
    public function __construct($expected)
    {
        if (!is_scalar($expected) && null != $expected) {
            throw new IllegalArgumentException('Can only compare scalar values and null.');
        }

        $this->expected = $expected;
    }

    /**
     * test that the given value is eqal in content and type to the expected value
     *
     * @param   scalar|null  $value
     * @return  bool         true if value is equal to expected value, else false
     */
    public function test($value)
    {
        return $this->expected === $value;
    }
}
