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
use stubbles\lang\exception\RuntimeException;
/**
 * Predicate to ensure a value complies to a given regular expression.
 *
 * The predicate uses preg_match() and checks if the value occurs exactly
 * one time. Please make sure that the supplied regular expresion contains
 * correct delimiters, they will not be applied automatically. The test()
 * method throws a runtime exception in case the regular expression is invalid.
 *
 * @api
 * @since  4.0.0
 */
class Regex extends Predicate
{
    /**
     * the regular expression to use for validation
     *
     * @type  string
     */
    private $regex;

    /**
     * constructor
     *
     * @param  string  $regex  regular expression to use for validation
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * test that the given value complies with the regular expression
     *
     * @param   mixed  $value
     * @return  bool
     * @throws  RuntimeException  in case the used regular expresion is invalid
     */
    public function test($value)
    {
        $check = @preg_match($this->regex, $value);
        if (false === $check) {
            throw new RuntimeException('Invalid regular expression ' . $this->regex);
        }

        return ((1 != $check) ? (false) : (true));
    }
}
