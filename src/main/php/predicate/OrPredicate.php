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
 * Predicate which tests that two other predicates are true.
 *
 * @since  4.0.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class OrPredicate extends Predicate
{
    use CombinedPredicate;

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        return $this->predicate1->test($value) || $this->predicate2->test($value);
    }
}
