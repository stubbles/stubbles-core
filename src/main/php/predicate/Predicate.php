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
 * Evaluates if a given value fulfills a criteria.
 *
 * @api
 * @since  4.0.0
 * @method  \stubbles\predicate\Predicate  and(\stubbles\predicate\Predicate|callable $predicate)
 * @method  \stubbles\predicate\Predicate  or(\stubbles\predicate\Predicate|callable $predicate)
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
abstract class Predicate
{
    /**
     * casts given predicate to a predicate instance
     *
     * @param   \stubbles\predicate\Predicate|callable  $predicate
     * @return  \stubbles\predicate\Predicate
     * @throws  \InvalidArgumentException
     */
    public static function castFrom($predicate)
    {
        if ($predicate instanceof self) {
            return $predicate;
        } elseif (is_callable($predicate)) {
            return new CallablePredicate($predicate);
        }

        throw new \InvalidArgumentException(
                'Given predicate is neither a callable nor an instance of ' . __CLASS__
        );
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public abstract function test($value);

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function __invoke($value)
    {
        return $this->test($value);
    }

    /**
     * combines this with another predicate to a predicate which requires both to be true
     *
     * @param   \stubbles\predicate\Predicate|callable  $other
     * @return  \stubbles\predicate\Predicate
     * @deprecated  since 7.0.0, use or($other) instead, will be removed with 8.0.0
     */
    public function asWellAs($other)
    {
        return new AndPredicate($this, self::castFrom($other));
    }

    /**
     * combines this with another predicate to a predicate which requires on of them to be true
     *
     * @param   \stubbles\predicate\Predicate|callable  $other
     * @return  \stubbles\predicate\Predicate
     * @deprecated  since 7.0.0, use or($other) instead, will be removed with 8.0.0
     */
    public function orElse($other)
    {
        return new OrPredicate($this, self::castFrom($other));
    }

    /**
     * provide utility methods "and" and "or" to combine predicates
     *
     * @param   string   $method
     * @param   mixed[]  $arguments
     * @return  \stubbles\predicate\Predicate
     * @throws  \BadMethodCallException
     * @since   1.4.0
     */
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'and':
                return $this->asWellAs(...$arguments);

            case 'or':
                return $this->orElse(...$arguments);

            default:
                throw new \BadMethodCallException(
                        'Call to undefined method '
                        . get_class($this) . '->' . $method . '()'
                );
        }
    }

    /**
     * returns a negated version of this predicate
     *
     * @return  \stubbles\predicate\Predicate
     */
    public function negate()
    {
        return new NegatePredicate($this);
    }
}
