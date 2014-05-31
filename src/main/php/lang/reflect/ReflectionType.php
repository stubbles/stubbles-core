<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
/**
 * Basic interface for type references.
 *
 * @api
 */
interface ReflectionType
{
    /**
     * returns the name of the type
     *
     * @return  string
     */
    public function getName();

    /**
     * checks whether the type is an object
     *
     * @return  bool
     */
    public function isObject();

    /**
     * checks whether the type is a primitive
     *
     * @return  bool
     */
    public function isPrimitive();

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare);

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString();
}
