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
 * Common instance creation for predicates combining two other predicates.
 *
 * @since  4.0.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
trait CombinedPredicate
{
    /**
     * @type  Predicate
     */
    private $predicate1;
    /**
     * @type  Predicate
     */
    private $predicate2;

    /**
     * constructor
     *
     * @param  \stubbles\predicate\Predicate|callable  $predicate1
     * @param  \stubbles\predicate\Predicate|callable  $predicate2
     */
    public function __construct($predicate1, $predicate2)
    {
        $this->predicate1 = Predicate::castFrom($predicate1);
        $this->predicate2 = Predicate::castFrom($predicate2);
    }
}
