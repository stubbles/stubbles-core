<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect {
    use stubbles\lang;
    use stubbles\lang\Sequence;
    use stubbles\lang\reflect\annotation\AnnotationCache;
    use stubbles\lang\reflect\annotation\Annotations;
    use stubbles\lang\reflect\annotation\parser\AnnotationStateParser;

    /**
     * returns annotations for given reflected
     *
     * @param   \Reflector|string|object  $reflected   class name, function name of or object instance to reflect
     * @param   string                    $methodName  optional  specific method to reflect
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.3.0
     */
    function annotationsOf($reflected, $methodName = null)
    {
        $reflector = ($reflected instanceof \Reflector) ? $reflected : lang\reflect($reflected, $methodName);
        $target    = _annotationTarget($reflector);
        if (AnnotationCache::has($target)) {
            return AnnotationCache::get($target);
        }

        list($sourceTarget) = explode('#', $target);
        $return = null;
        foreach (AnnotationStateParser::parseFrom(docComment($reflector), $sourceTarget) as $annotations) {
            AnnotationCache::put($annotations);
            if ($annotations->target() === $target) {
                $return = $annotations;
            }
        }

        if (null === $return) {
            $return = new Annotations($target);
            AnnotationCache::put($return);
        }

        return $return;
    }

    /**
     * returns annotations of constructor of given reflected
     *
     * @param   \ReflectionClass|string|object  $reflected   class name, class instance of or object instance to reflect constructor annotations of
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.3.0
     */
    function annotationsOfConstructor($reflected)
    {
        return annotationsOf(
                ($reflected instanceof \ReflectionClass) ? $reflected->getConstructor() : lang\reflectConstructor($reflected)
        );
    }

    /**
     * returns annotations for given parameter
     *
     * @param   string                                           $name             name of parameter to retrieve annotations for
     * @param   string|object|array|\ReflectionFunctionAbstract  $classOrFunction  something that references a function or a class
     * @param   string                                           $methodName       optional  in case first param references a class
     * @return
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.3.0
     */
    function annotationsOfParameter($name, $classOrFunction, $methodName = null)
    {
        return annotationsOf(parameter($name, $classOrFunction, $methodName));
    }

    /**
     * retrieves parameter with given name from referenced function or method
     *
     * @param   string                                           $name             name of parameter to retrieve
     * @param   string|object|array|\ReflectionFunctionAbstract  $classOrFunction  something that references a function or a class
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.3.0
     */
    function annotationsOfConstructorParameter($name, $classOrFunction)
    {
        return annotationsOf(constructorParameter($name, $classOrFunction));
    }

    /**
     * returns annotation target for given reflector
     *
     * @param  \Reflector $reflector
     * @return  string
     * @throws  \ReflectionException
     * @since   5.3.0
     */
    function _annotationTarget(\Reflector $reflector)
    {
        if ($reflector instanceof \ReflectionClass) {
            return $reflector->getName();
        }

        if ($reflector instanceof \ReflectionMethod) {
            return $reflector->class . '::' . $reflector->getName() . '()';
        }

        if ($reflector instanceof \ReflectionFunction) {
            return $reflector->getName() . '()';
        }

        if ($reflector instanceof \ReflectionParameter) {
            return _annotationTarget($reflector->getDeclaringFunction()) . '#' . $reflector->getName();
        }

        if ($reflector instanceof \ReflectionProperty) {
            return $reflector->class . ($reflector->isStatic() ? '::$' : '->') . $reflector->getName();
        }

        throw new \ReflectionException('Can not retrieve target for ' . get_class($reflector));
    }

    /**
     * returns doc comment for given reflector
     *
     * @param   \Reflector  $reflector
     * @return  \stubbles\lang\reflect\annotation\Annotations[]
     * @throws  \ReflectionException
     * @since   5.3.0
     */
    function docComment(\Reflector $reflector)
    {
        if ($reflector instanceof \ReflectionClass
                || $reflector instanceof \ReflectionFunctionAbstract
                || $reflector instanceof \ReflectionProperty) {
            return $reflector->getDocComment();
        }

        if ($reflector instanceof \ReflectionParameter) {
            return $reflector->getDeclaringFunction()->getDocComment();
        }

        throw new \ReflectionException('Can not retrieve doc comment for ' . get_class($reflector));
    }

    /**
     * returns a sequence of all methods of given class
     *
     * @param   string|object|\ReflectionClass  $class   class to return methods for
     * @param   int                             $filter  optional  filter the results to include only methods with certain attributes using any combination of ReflectionMethod::IS_ constants
     * @return  \stubbles\lang\Sequence
     * @throws  \InvalidArgumentException
     * @since   5.3.0
     */
    function methodsOf($class, $filter = null)
    {
        if (!($class instanceof \ReflectionClass)) {
            $class = lang\reflect($class);
            if (!($class instanceof \ReflectionClass)) {
                throw new \InvalidArgumentException('Given class must be a class name, a class instance or an instance of \ReflectionClass');
            }
        }

        return Sequence::of($class->getMethods($filter));
    }

    /**
     * returns a sequence of all properties of given class
     *
     * @param   string|object|\ReflectionClass  $class   class to return properties for
     * @param   int                             $filter  optional  filter the results to include only properties with certain attributes using any combination of ReflectionProperty::IS_ constants
     * @return  \stubbles\lang\Sequence
     * @throws  \InvalidArgumentException
     * @since   5.3.0
     */
    function propertiesOf($class, $filter = null)
    {
        if (!($class instanceof \ReflectionClass)) {
            $class = lang\reflect($class);
            if (!($class instanceof \ReflectionClass)) {
                throw new \InvalidArgumentException('Given class must be a class name, a class instance or an instance of \ReflectionClass');
            }
        }

        return Sequence::of($class->getProperties($filter));
    }

    /**
     * returns sequence of parameters of a function or method
     *
     * @param   string|object|array|\ReflectionFunctionAbstract  $classOrFunction  something that references a function or a class
     * @param   string                                           $methodName       optional  name of method in case first param references a class
     * @return  \stubbles\lang\Sequence
     * @throws  \InvalidArgumentException
     * @since   5.3.0
     */
    function parametersOf($classOrFunction, $methodName = null)
    {
        if (!($classOrFunction instanceof \ReflectionFunctionAbstract)) {
            $function = lang\reflect($classOrFunction, $methodName);
            if (!($function instanceof \ReflectionFunctionAbstract)) {
                throw new \InvalidArgumentException('Given function must be a function name, a method reference or an instance of \ReflectionFunctionAbstract');
            }
        } else {
            $function = $classOrFunction;
        }

        return Sequence::of($function->getParameters());
    }

    /**
     * returns constructor parameters for given class
     *
     * @param   string|object|\ReflectionClass  $class  something that references a function or a class
     * @return  \stubbles\lang\Sequence
     * @since   5.3.0
     */
    function parametersOfConstructor($class)
    {
        return parametersOf($class, '__construct');
    }

    /**
     * retrieves parameter with given name from referenced function or method
     *
     * @param   string                                           $name             name of parameter to retrieve
     * @param   string|object|array|\ReflectionFunctionAbstract  $classOrFunction  something that references a function or a class
     * @param   string                                           $methodName       optional  in case first param references a class
     * @return  \ReflectionParameter
     * @since   5.3.0
     */
    function parameter($name, $classOrFunction, $methodName = null)
    {
        return parametersOf($classOrFunction, $methodName)
                ->filter(
                        function(\ReflectionParameter $parameter) use ($name)
                        {
                            return $parameter->getName() === $name;
                        }
        )->first();
    }

    /**
     * retrieves parameter with given name from constructor of referenced class
     *
     * @param   string                                           $name             name of parameter to retrieve
     * @param   string|object|array|\ReflectionFunctionAbstract  $classOrFunction  something that references a function or a class
     * @return  \ReflectionParameter
     * @since   5.3.0
     */
    function constructorParameter($name, $classOrFunction)
    {
        return parameter($name, $classOrFunction, '__construct');
    }
}
