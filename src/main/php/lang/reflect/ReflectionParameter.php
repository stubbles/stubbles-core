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
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\lang\reflect\annotation\AnnotationFactory;
/**
 * Extended Reflection class for parameters.
 *
 * @api
 */
class ReflectionParameter extends \ReflectionParameter implements Annotatable
{
    /**
     * name of reflected routine
     *
     * @type  string
     */
    protected $routineName;
    /**
     * reflection instance of routine containing this parameter
     *
     * @type  \stubbles\lang\reflect\ReflectionRoutine
     */
    protected $refRoutine;
    /**
     * name of reflected parameter
     *
     * @type  string
     */
    protected $paramName;

    /**
     * constructor
     *
     * @param  string|array|\stubbles\lang\reflect\ReflectionRoutine  $routine    name or reflection instance of routine
     * @param  string                                                 $paramName  name of parameter to reflect
     */
    public function __construct($routine, $paramName)
    {
        if ($routine instanceof ReflectionMethod) {
            $refRoutine  = $routine;
            $routineName = array($routine->getDeclaringClass()->getName(), $routine->getName());
        } elseif ($routine instanceof ReflectionFunction) {
            $refRoutine  = $routine;
            $routineName = $routine->getName();
        } else {
            $refRoutine  = null;
            $routineName = $routine;
        }

        parent::__construct($routineName, $paramName);
        $this->refRoutine  = $refRoutine;
        $this->routineName = $routineName;
        $this->paramName   = $paramName;
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        $refRoutine = $this->getDeclaringFunction();
        $targetName = ((is_array($this->routineName)) ? ($this->routineName[0] . '::' . $this->routineName[1] . '()') : ($this->routineName));
        return AnnotationFactory::has($refRoutine->getDocComment(), $annotationName . '#' . $this->paramName, $targetName);
    }

    /**
     * return the specified annotation
     *
     * @param   string          $annotationName
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    public function getAnnotation($annotationName)
    {
        $refRoutine = $this->getDeclaringFunction();
        $targetName = ((is_array($this->routineName)) ? ($this->routineName[0] . '::' . $this->routineName[1] . '()') : ($this->routineName));
        return AnnotationFactory::create($refRoutine->getDocComment(), $annotationName . '#' . $this->paramName, $targetName);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if (($compare instanceof self) == false) {
            return false;
        }

        $class        = $this->getDeclaringClass();
        $compareClass = $compare->getDeclaringClass();
        if ((null == $class && null != $compareClass) || null != $class && null == $compareClass) {
            return false;
        }

        if (null == $class) {
            return ($compare->routineName == $this->routineName && $compare->paramName == $this->paramName);
        }

        return ($compareClass->getName() == $class->getName() && $compare->routineName == $this->routineName && $compare->paramName == $this->paramName);
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionParameter['[name-of-reflected-class]'::'[name-of-reflected-function]'(): Argument '[name-of-reflected-argument]']  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionParameter[MyClass::myMethod(): Argument foo] {
     * }
     * stubbles\lang\reflect\ReflectionParameter[myFunction(): Argument bar] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        if (is_array($this->routineName) == false) {
            return __CLASS__ . '[' . $this->routineName . '(): Argument ' . $this->paramName . "] {\n}\n";
        }

        return __CLASS__ . '[' . $this->routineName[0] . '::' . $this->routineName[1] . '(): Argument ' . $this->paramName . "] {\n}\n";
    }

    /**
     * helper method to return the reflection routine defining this parameter
     *
     * @return  \stubbles\lang\reflect\ReflectionRoutine
     */
    public function getDeclaringFunction()
    {
        if (null === $this->refRoutine) {
            if (is_array($this->routineName)) {
                $this->refRoutine = new ReflectionMethod($this->routineName[0], $this->routineName[1]);
            } else {
                $this->refRoutine = new ReflectionFunction($this->routineName);
            }
        }

        return $this->refRoutine;
    }

    /**
     * returns the class that declares this parameter
     *
     * @return  \stubbles\lang\reflect\ReflectionClass
     */
    public function getDeclaringClass()
    {
        if (!is_array($this->routineName)) {
            return null;
        }

        return new ReflectionClass(parent::getDeclaringClass()->getName());
    }

    /**
     * returns the type (class) hint for this parameter
     *
     * @return  \stubbles\lang\reflect\ReflectionClass
     */
    public function getClass()
    {
        $refClass = parent::getClass();
        if (null === $refClass) {
            return null;
        }

        return new ReflectionClass($refClass->getName());
    }

    /**
     * returns parameter type information
     *
     * @return  \stubbles\lang\reflect\ReflectionType
     * @throws  \ReflectionException
     */
    public function getType()
    {
        $refClass = $this->getClass();
        if (null !== $refClass) {
            return $refClass;
        }

        if ($this->isArray()) {
            return ReflectionPrimitive::$ARRAY;
        }

        $paramType = $this->parseParamType();
        if (null === $paramType) {
            throw new \ReflectionException('Can\'t parse type from doc comment for ' . $this->getDeclaringFunction()->getName() . ' param ' . $this->getName());
        }

        return \stubbles\lang\typeFor($paramType);
    }

    /**
     * parses param type from doc comment
     *
     * @return  string
     */
    private function parseParamType()
    {
        $docComment = $this->getDeclaringFunction()->getDocComment();
        if (null == $docComment) {
            return null;
        }

        $pos = 0;
        foreach (explode("\n", $docComment) as $line) {
            $paramPart = strstr($line, '@param');
            if (false === $paramPart) {
                continue;
            }

            if ($this->getPosition() !== $pos) {
                $pos++;
                continue;
            }

            $paramParts = explode(' ', trim(str_replace('@param', '', $paramPart)));
            return trim($paramParts[0]);
        }

        return null;
    }
}
