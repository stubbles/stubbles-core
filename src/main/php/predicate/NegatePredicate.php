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
 * Negates evaluation of wrapped predicate.
 *
 * @since  4.0.0
 */
class NegatePredicate extends Predicate
{
    /**
     * @type  Predicate
     */
    private $predicate;

    /**
     * constructor
     *
     * @param  \stubbles\predicate\Predicate|callable  $predicate
     */
    public function __construct($predicate)
    {
        $this->predicate = Predicate::castFrom($predicate);
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        return !$this->predicate->test($value);
    }
}
