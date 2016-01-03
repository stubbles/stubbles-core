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
 * Trait for predicates which can be reused.
 *
 * @since  4.0.0
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
trait ReusablePredicate
{
    /**
     * reusable instance
     *
     * @type  Predicate
     */
    private static $instance;

    /**
     * returns reusable predicate instance
     *
     * @return  \stubbles\predicate\Predicate
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
