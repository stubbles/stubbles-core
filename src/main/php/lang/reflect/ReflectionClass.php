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
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\lang\reflect\annotation\AnnotationFactory;
use stubbles\lang\reflect\matcher\MethodMatcher;
use stubbles\lang\reflect\matcher\PropertyMatcher;
/**
 * Extended Reflection class for classes that allows usage of annotations.
 *
 * @api
 */
class ReflectionClass extends \ReflectionClass implements BaseReflectionClass
{
    /**
     * creates a new instance for given class name
     *
     * @param   string  $className
     * @return  \stubbles\lang\reflect\ReflectionClass
     * @since   3.0.0
     */
    public static function fromName($className)
    {
        return new self($className);
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return AnnotationFactory::has($this->getDocComment(), $annotationName, Annotation::TARGET_CLASS, $this->getName());
    }

    /**
     * return the specified annotation
     *
     * @param   string  $annotationName
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    public function getAnnotation($annotationName)
    {
        return AnnotationFactory::create($this->getDocComment(), $annotationName, Annotation::TARGET_CLASS, $this->getName());
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->getName() == $this->getName());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionClass['[name-of-reflected-class]']  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionClass[MyClass] {
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
     * @param   string   $name  name of method to return
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
     * @param   int   $filter  desired method types
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
     * @return  \stubbles\lang\reflect\ReflectionProperty]|
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
            $stubRefClasses[] = new self($interface->getName());
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

        return new self($parentClass->getName());
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
