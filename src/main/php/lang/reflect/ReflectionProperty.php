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
use stubbles\lang\reflect\annotation\Annotatable;
use stubbles\lang\reflect\annotation\Annotated;
/**
 * Extended Reflection class for class properties that allows usage of annotations.
 *
 * In difference to the default setting of PHP a stub-reflected property will
 * always be accessible by default, regardless of whether it was defined as
 * public, protected or private.
 *
 * @api
 */
class ReflectionProperty extends \ReflectionProperty implements Annotatable
{
    use Annotated;

    /**
     * Name of the class
     *
     * @type  string
     */
    protected $className;
    /**
     * reflection instance for class declaring this property
     *
     * @type  \stubbles\lang\reflect\BaseReflectionClass
     */
    protected $refClass;
    /**
     * Name of the property
     *
     * @type  string
     */
    protected $propertyName;

    /**
     * constructor
     *
     * @param  string|\stubbles\lang\reflect\BaseReflectionClass  $class         name of class to reflect
     * @param  string                                             $propertyName  name of property to reflect
     */
    public function __construct($class, $propertyName)
    {
        if ($class instanceof BaseReflectionClass) {
            $refClass  = $class;
            $className = $class->getName();
        } else {
            $refClass  = null;
            $className = $class;
        }

        parent::__construct($className, $propertyName);
        $this->refClass     = $refClass;
        $this->className    = $className;
        $this->propertyName = $propertyName;
        $this->setAccessible(true);
    }

    /**
     * target name of property annotations
     *
     * @return  string
     * @see     \stubbles\lang\reflect\annotation\Annotated
     */
    protected function annotationTargetName()
    {
        return $this->className . ($this->isStatic() ? '::$' : '->') . $this->propertyName;
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
            return ($compare->className == $this->className && $compare->propertyName == $this->propertyName);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionProperty['[name-of-reflected-class]'::'[name-of-reflected-property]']  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionProperty[MyClass::myProperty] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->className . '::' . $this->propertyName . "] {\n}\n";
    }

    /**
     * returns the class that declares this parameter
     *
     * @return  \stubbles\lang\reflect\BaseReflectionClass
     */
    public function getDeclaringClass()
    {
        $refClass = parent::getDeclaringClass();
        if ($refClass->getName() === $this->className) {
            if (null === $this->refClass) {
                $this->refClass = new ReflectionClass($this->className);
            }

            return $this->refClass;
        }

        return new ReflectionClass($refClass->getName());
    }
}
