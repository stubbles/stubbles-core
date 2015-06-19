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
    private static $null;
    /**
     * @type  mixed
     */
    private $value;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$null = new self(null);
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
            return self::$null;
        }

        return new self($value);
    }

    /**
     * checks if a value is present
     *
     * Present means the value is not null.
     *
     * @return  bool
     */
    public function isPresent()
    {
        return null !== $this->value;
    }

    /**
     * checks if value is empty
     *
     * @return  bool
     * @since   6.2.0
     */
    public function isEmpty()
    {
        if (is_int($this->value)) {
            return false;
        }

        return empty($this->value);
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
     *
     * In case the value is null or or doesn't fulfill the predicate the return
     * value is a null result.
     *
     * @param   callable  $predicate
     * @return  \stubbles\lang\Result
     */
    public function filter(callable $predicate)
    {
        if ($this->isPresent() && $predicate($this->value)) {
            return $this;
        }

        return self::$null;
    }

    /**
     * maps the value using mapper into a different result
     *
     * In case the value is null the return value still is a null result.
     *
     * @param   callable  $mapper
     * @return  \stubbles\lang\Result
     */
    public function map(callable $mapper)
    {
        if ($this->isPresent()) {
            return new self($mapper($this->value));
        }

        return self::$null;
    }

    /**
     * returns the result if value is present, or result of other
     *
     * @param   mixed  $other
     * @return  \stubbles\lang\Result
     */
    public function whenNull($other)
    {
        if ($this->isPresent()) {
            return $this;
        }

        return self::of($other);
    }

    /**
     * returns the result if value is present, or the result of applied other
     *
     * @param   callable  $other
     * @return  \stubbles\lang\Result
     */
    public function applyWhenNull(callable $other)
    {
        if ($this->isPresent()) {
            return $this;
        }

        return self::of($other());
    }

    /**
     * returns the result if value is not empty, or result of other
     *
     * @param   mixed  $other
     * @return  \stubbles\lang\Result
     * @since   6.2.0
     */
    public function whenEmpty($other)
    {
        if (!$this->isEmpty($this->value)) {
            return $this;
        }

        return self::of($other);
    }

    /**
     * returns the result if value is not empty, or the result of applied other
     *
     * @param   callable  $other
     * @return  \stubbles\lang\Result
     * @since   6.2.0
     */
    public function applyWhenEmpty(callable $other)
    {
        if (!$this->isEmpty($this->value)) {
            return $this;
        }

        return self::of($other());
    }
}
Result::__static();
