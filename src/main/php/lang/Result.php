<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * Enables to wrap a return value.
 *
 * In other languages or libraries this is known as an Optional, but I think
 * this is a very bad name.
 *
 * @since  6.0.0
 */
class Result
{
    /**
     * @type  \stubbles\lang\Result
     */
    private static $empty;
    /**
     * @type  mixed
     */
    private $value;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$empty = new self(null);
    }

    /**
     * constructor
     *
     * @param  mixed  $value  actual result value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * static constructor
     *
     * @param   mixed   $value  actual result value
     * @return  \stubbles\lang\Result
     */
    public static function of($value)
    {
        if (null === $value) {
            return self::$empty;
        }

        return new self($value);
    }

    /**
     * checks if a value is present
     *
     * @return  bool
     */
    public function isPresent()
    {
        return null !== $this->value;
    }

    /**
     * returns actual value
     *
     * @return  mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * returns result when value is present and fulfills the predicate
     * In case the value is null or or doesn't fulfill the predicate the return
     * value is an empty result.
     *
     * @param   callable  $predicate
     * @return  \stubbles\lang\Result
     */
    public function filter(callable $predicate)
    {
        if ($this->isPresent() && $predicate($this->value)) {
            return $this;
        }

        return self::$empty;
    }

    /**
     * maps the value using mapper into a different result
     *
     * @param   callable  $mapper
     * @return  \stubbles\lang\Result
     */
    public function map(callable $mapper)
    {
        if ($this->isPresent()) {
            return new self($mapper($this->value));
        }

        return self::$empty;
    }

    /**
     * returns the value if present, or given other
     *
     * @param   mixed  $other
     * @return  mixed
     */
    public function whenNull($other)
    {
        if ($this->isPresent()) {
            return $this->value;
        }

        return $other;
    }

    /**
     * returns the value if present, or the result of other
     *
     * @param   callable  $other
     * @return  mixed
     */
    public function applyWhenNull(callable $other)
    {
        if ($this->isPresent()) {
            return $this->value;
        }

        return $other();
    }
}
Result::__static();
