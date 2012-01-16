<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect;
use net\stubbles\lang\reflect\annotation\Annotatable;
use net\stubbles\lang\reflect\matcher\MethodMatcher;
use net\stubbles\lang\reflect\matcher\PropertyMatcher;
/**
 * Marker interface for ReflectionClass and ReflectionObject.
 *
 * This interface allows to use
 * net\stubbles\lang\reflect\ReflectionClass and
 * net\stubbles\lang\reflect\ReflectionObject on
 * the same argument when the argument is typehinted with this interface.
 */
interface BaseReflectionClass extends ReflectionType, Annotatable
{
    /**
     * returns the constructor or null if none exists
     *
     * @return  ReflectionMethod
     */
    public function getConstructor();

    /**
     * returns the specified method or null if it does not exist
     *
     * @param   string  $name  name of method to return
     * @return  ReflectionMethod
     */
    public function getMethod($name);

    /**
     * returns a list of all methods
     *
     * @param   int   $filter  desired method types
     * @return  ReflectionMethod[]
     */
    public function getMethods($filter = null);

    /**
     * returns a list of all methods which satify the given matcher
     *
     * @param   MethodMatcher  $methodMatcher
     * @return  ReflectionMethod[]
     */
    public function getMethodsByMatcher(MethodMatcher $methodMatcher);

    /**
     * returns the specified property or null if it does not exist
     *
     * @param   string  $name  name of property to return
     * @return  ReflectionProperty
     */
    public function getProperty($name);

    /**
     * returns a list of all properties
     *
     * @param   int  $filter  desired property types
     * @return  ReflectionProperty[]
     */
    public function getProperties($filter = null);

    /**
     * returns a list of all properties which satify the given matcher
     *
     * @param   PropertyMatcher   $propertyMatcher
     * @return  ReflectionProperty[]
     */
    public function getPropertiesByMatcher(PropertyMatcher $propertyMatcher);

    /**
     * returns a list of all interfaces
     *
     * @return  ReflectionClass[]
     */
    public function getInterfaces();

    /**
     * returns a list of all interfaces
     *
     * @return  ReflectionClass
     */
    public function getParentClass();

    /**
     * returns the extension to where this class belongs too
     *
     * @return  ReflectionExtension
     */
    public function getExtension();

    /**
     * checks whether class implements a certain interface
     *
     * @param   string  $interface
     * @return  bool
     */
    public function implementsInterface($interface);
}
?>