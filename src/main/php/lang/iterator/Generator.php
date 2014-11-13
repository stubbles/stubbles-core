<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
/**
 * Iterator which calls an operation to retrieve the value.
 *
 * @since  5.2.0
 */
class Generator implements \Iterator
{
    /**
     * initial value
     *
     * @type  mixed
     */
    private $seed;
    /**
     * current value
     *
     * @type  mixed
     */
    private $value;
    /**
     * number of delivered elements since last rewind
     *
     * @type  int
     */
    private $elementsGenerated = 0;
    /**
     * operation which takes a value and generates a new one
     *
     * @type  callable
     */
    private $operation;
    /**
     * function which decides whether a value is valid
     *
     * @type  callable
     */
    private $validator;

    /**
     * constructor
     *
     * @param  $seed     $seed       initial value
     * @param  callable  $operation  operation which takes a value and generates a new one
     * @param  callable  $validator  function which decides whether a value is valid
     */
    public function __construct($seed, callable $operation, callable $validator)
    {
        $this->seed      = $seed;
        $this->value     = $seed;
        $this->operation = $operation;
        $this->validator = $validator;
    }

    /**
     * creates a generator which iterates infinitely
     *
     * @param   $seed     $seed       initial value
     * @param   callable  $operation  operation which takes a value and generates a new one
     * @return  Generator
     */
    public static function infinite($seed, callable $operation)
    {
        return new self($seed, $operation, function() { return true; });
    }

    /**
     * returns the current generated value
     *
     * @return  string
     */
    public function current()
    {
        return $this->value;
    }

    /**
     * returns number of delivered elements since last rewind()
     *
     * @return  int
     */
    public function key()
    {
        return $this->elementsGenerated;
    }

    /**
     * generates next value
     */
    public function next()
    {
        $operation   = $this->operation;
        $this->value = $operation($this->value);
        $this->elementsGenerated++;
    }

    /**
     * resets number of delivered elements to 0 and restarts with initial seed
     */
    public function rewind()
    {
        $this->elementsGenerated = 0;
        $this->value             = $this->seed;
    }

    /**
     * checks if current element is valid
     *
     * @return  string
     */
    public function valid()
    {
        $validate = $this->validator;
        return $validate($this->value, $this->elementsGenerated);
    }
}
