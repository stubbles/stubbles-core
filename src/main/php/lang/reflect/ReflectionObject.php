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
use stubbles\lang\reflect\annotation\Annotated;
use stubbles\lang\reflect\matcher\MethodMatcher;
use stubbles\lang\reflect\matcher\PropertyMatcher;
/**
 * Extended Reflection class for classes that allows usage of annotations.
 *
 * @api
 */
class ReflectionObject extends \ReflectionObject implements BaseReflectionClass
{
    use Annotated;

    /**
     * instance of the reflected class
     *
     * @type  object
     */
    protected $classObject;

    /**
     * constructor
     *
     * @param  object  $classObject  instance of class to reflect
     */
    public function __construct($classObject)
    {
        parent::__construct($classObject);
        $this->classObject = $classObject;
    }

    /**
     * creates new instance for given object
     *
     * @param   object  $object
     * @return  \stubbles\lang\reflect\ReflectionObject
     * @since   3.0.0
     */
    public static function fromInstance($object)
    {
        return new self($object);
    }

    /**
     * target name of property annotations
     *
     * @return  string
     * @see     \stubbles\lang\reflect\annotation\Annotated
     */
    protected function annotationTargetName()
    {
        return $this->getName();
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if (!($compare instanceof self)) {
            return false;
        }

        return ($compare->getName() == $this->getName()
          && spl_object_hash($compare->classObject) == spl_object_hash($this->classObject));
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionObject['[name-of-reflected-class]']  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionObject[MyClass] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->getName() . "] {\n}\n";
    }

    /**
     * returns the instance of the class with with this reflection instance was created
     *
     * @return  object
     */
    public function getObjectInstance()
    {
        return $this->classObject;
    }

    /**
     * returns the constructor or null if none exists
     *
     * @return  \stubbles\lang\reflect\ReflectionMethod
     */
    public function getConstructor()
    {
        return $this->getMethod('__construct');
    }

    /**
     * returns the specified method or null if it does not exist
     *
     * @param   string  $name  name of method to return
     * @return  \stubbles\lang\reflect\ReflectionMethod
     */
    public function getMethod($name)
    {
        if (parent::hasMethod($name) == false) {
            return null;
        }

        return new ReflectionMethod($this, $name);
    }

    /**
     * returns a list of all methods
     *
     * @param   int  $filter  desired method types
     * @return  \stubbles\lang\reflect\ReflectionMethod[]
     */
    public function getMethods($filter = null)
    {
        if (null === $filter) {
            $methods = parent::getMethods();
        } else {
            $methods = parent::getMethods($filter);
        }

        $stubMethods = [];
        foreach ($methods as $method) {
            $stubMethods[] = new ReflectionMethod($this, $method->getName());
        }

        return $stubMethods;
    }

    /**
     * returns a list of all methods which satify the given matcher
     *
     * @param   \stubbles\lang\reflect\matcher\MethodMatcher  $methodMatcher
     * @return  \stubbles\lang\reflect\ReflectionMethod[]
     */
    public function getMethodsByMatcher(MethodMatcher $methodMatcher)
    {
        $methods     = parent::getMethods();
        $stubMethods = [];
        foreach ($methods as $method) {
            if ($methodMatcher->matchesMethod($method)) {
                $stubMethod = new ReflectionMethod($this, $method->getName());
                if ($methodMatcher->matchesAnnotatableMethod($stubMethod)) {
                    $stubMethods[] = $stubMethod;
                }
            }
        }

        return $stubMethods;
    }

    /**
     * returns the specified property or null if it does not exist
     *
     * @param   string  $name  name of property to return
     * @return  \stubbles\lang\reflect\ReflectionProperty
     */
    public function getProperty($name)
    {
        if (parent::hasProperty($name) == false) {
            return null;
        }

        return new ReflectionProperty($this, $name);
    }

    /**
     * returns a list of all properties
     *
     * @param   int  $filter  desired property types
     * @return  \stubbles\lang\reflect\ReflectionProperty[]
     */
    public function getProperties($filter = null)
    {
        if (null === $filter) {
            $properties = parent::getProperties();
        } else {
            $properties = parent::getProperties($filter);
        }

        $stubProperties = [];
        foreach ($properties as $property) {
            $stubProperties[] = new ReflectionProperty($this, $property->getName());
        }

        return $stubProperties;
    }

    /**
     * returns a list of all properties which satify the given matcher
     *
     * @param   \stubbles\lang\reflect\matcher\PropertyMatcher  $propertyMatcher
     * @return  \stubbles\lang\reflect\ReflectionProperty[]
     */
    public function getPropertiesByMatcher(PropertyMatcher $propertyMatcher)
    {
        $properties     = parent::getProperties();
        $stubProperties = [];
        foreach ($properties as $property) {
            if ($propertyMatcher->matchesProperty($property)) {
                $stubProperty = new ReflectionProperty($this, $property->getName());
                if ($propertyMatcher->matchesAnnotatableProperty($stubProperty)) {
                    $stubProperties[] = $stubProperty;
                }
            }
        }

        return $stubProperties;
    }

    /**
     * returns a list of all interfaces
     *
     * @return  \stubbles\lang\reflect\ReflectionClass[]
     */
    public function getInterfaces()
    {
        $interfaces     = parent::getInterfaces();
        $stubRefClasses = [];
        foreach ($interfaces as $interface) {
            $stubRefClasses[] = new ReflectionClass($interface->getName());
        }

        return $stubRefClasses;
    }

    /**
     * returns a list of all interfaces
     *
     * @return  \stubbles\lang\reflect\ReflectionClass
     */
    public function getParentClass()
    {
        $parentClass  = parent::getParentClass();
        if (null === $parentClass || false === $parentClass) {
            return null;
        }

        return new ReflectionClass($parentClass->getName());
    }

    /**
     * returns the extension to where this class belongs too
     *
     * @return  \stubbles\lang\reflect\ReflectionExtension
     */
    public function getExtension()
    {
        $extensionName  = $this->getExtensionName();
        if (null === $extensionName || false === $extensionName) {
            return null;
        }

        return new ReflectionExtension($extensionName);
    }

    /**
     * checks whether the type is an object
     *
     * @return  bool
     */
    public function isObject()
    {
        return true;
    }

    /**
     * checks whether the type is a primitive
     *
     * @return  bool
     */
    public function isPrimitive()
    {
        return false;
    }
}
