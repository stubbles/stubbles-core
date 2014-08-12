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
use stubbles\lang\reflect\annotation\AnnotationFactory;
/**
 * Extended Reflection class for class methods that allows usage of annotations.
 *
 * @api
 */
class ReflectionMethod extends \ReflectionMethod implements ReflectionRoutine
{
    use Annotated;

    /**
     * name of the reflected class
     *
     * @type  string
     */
    protected $className;
    /**
     * declaring class
     *
     * @type  \stubbles\lang\reflect\BaseReflectionClass
     */
    protected $refClass;
    /**
     * name of the reflected method
     *
     * @type  string
     */
    protected $methodName;

    /**
     * constructor
     *
     * @param  string|\stubbles\lang\reflect\BaseReflectionClass  $class       name of class to reflect
     * @param  string                                             $methodName  name of method to reflect
     */
    public function __construct($class, $methodName)
    {
        if ($class instanceof BaseReflectionClass) {
            $refClass   = $class;
            $className  = $refClass->getName();
        } else if (is_object($class)) {
            $refClass  = null;
            $className = get_class($class);
        } else {
            $refClass   = null;
            $className  = $class;
        }

        parent::__construct($className, $methodName);
        $this->refClass   = $refClass;
        $this->className  = $className;
        $this->methodName = $methodName;
    }

    /**
     * target name of property annotations
     *
     * @return  string
     * @see     \stubbles\lang\reflect\annotation\Annotated
     */
    protected function annotationTargetName()
    {
        return $this->className . '::' . $this->methodName . '()';
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
            return ($compare->className === $this->className && $compare->methodName === $this->methodName);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionMethod['[name-of-reflected-class]'::'[name-of-reflected-method]'()]  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionMethod[MyClass::myMethod()] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->className . '::' . $this->methodName . "()] {\n}\n";
    }

    /**
     * returns the class that declares this method
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

    /**
     * returns a list of all parameters
     *
     * @return  \stubbles\lang\reflect\ReflectionParameter[]
     */
    public function getParameters()
    {
        $parameters     = parent::getParameters();
        $stubParameters = [];
        foreach ($parameters as $parameter) {
            $stubParameters[] = new ReflectionParameter($this, $parameter->getName());
        }

        return $stubParameters;
    }

    /**
     * returns information about the return type of a method
     *
     * If the return type is a class the return value is an instance of
     * stubbles\lang\reflect\ReflectionClass (if the class is unknown a
     * stubbles\ClassNotFoundException will be thrown), if it is a scalar
     * type the return value is an instance of ReflectionPrimitive, for mixed
     * and object the return value is an instance of MixedType, and if the
     * method does not have a return value this method returns null.
     * Please be aware that this is guessing from the doc block with which the
     * function is documented. If the doc block is missing or incorrect the
     * return value of this method may be wrong. This is due to missing type
     * hints for return values in PHP itself.
     *
     * @return  \stubbles\lang\reflect\ReflectionType
     */
    public function getReturnType()
    {
        $returnPart = strstr($this->getDocComment(), '@return');
        if (false === $returnPart) {
            return null;
        }

        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = trim($returnParts[0]);
        if ('void' === strtolower($returnType)) {
            return null;
        }

        return \stubbles\lang\typeFor($returnType);
    }

    /**
     * returns the extension to where this class belongs too
     *
     * @return  \stubbles\lang\reflect\ReflectionExtension
     * @since   2.0.0
     */
    public function getExtension()
    {
        $extensionName  = $this->getExtensionName();
        if (null === $extensionName || false === $extensionName) {
            return null;
        }

        return new ReflectionExtension($extensionName);
    }
}
