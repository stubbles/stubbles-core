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
use stubbles\lang;
/**
 * Allows to create new instances of given class or interface.
 *
 * @since  6.0.0
 */
class NewInstance
{
    /**
     * map of already evaluated classes
     *
     * @type  \ReflectionClass[]
     */
    private static $classes = [];

    /**
     * returns a new instance of the given class or interface
     *
     * @param   string|\ReflectionClass  $class            interface or class to create a new instance of
     * @param   array                    $callmap          map of functions to overwrite with according closure
     * @param   mixed[]                  $constructorArgs  list of arguments for the constructor
     * @return  $class
     * @throws  \InvalidArgumentException
     */
    public static function of($class, array $callmap = [], array $constructorArgs = [])
    {
        $clazz = lang\reflect($class);
        if (!($clazz instanceof \ReflectionClass)) {
            throw new \InvalidArgumentException(
                    'Given class ' . $class . ' does not resemble'
                    . ' a class or interface'
            );
        }

        if (!isset(self::$classes[$clazz->getName()])) {
            self::$classes[$clazz->getName()] = self::createCallMapClass($clazz);
        }

        return self::$classes[$clazz->getName()]
                ->newInstanceArgs($constructorArgs)
                ->mapCalls($callmap);
    }

    /**
     * creates a new class from the given class which uses the CallMap trait
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionClass
     * @throws  \ReflectionException
     */
    private static function createCallMapClass(\ReflectionClass $class)
    {
        $code = '';
        if ($class->inNamespace()) {
            $code .= 'namespace ' . $class->getNamespaceName() . " {\n";
        }

        $code .= 'class ' . $class->getShortName() . 'CallMap ';
        $code .= $class->isInterface() ? 'implements' : 'extends';
        $code .= ' \\' . $class->getName() . " {\n";
        $code .= "    use \stubbles\lang\\reflect\CallMap;\n";
        foreach (methodsOf($class)
                ->filter(
                        function(\ReflectionMethod $method)
                        {
                            return !$method->isPrivate()
                                    && !$method->isFinal()
                                    && !$method->isStatic()
                                    && !$method->isConstructor()
                                    && !$method->isDestructor();
                        }
                ) as $methodName => $method) {
            /* @var  $method \ReflectionMethod */
            $code .= '    ' . ($method->isPublic() ? 'public' : 'protected');
            $code .= ' function ' . $methodName . '(' . self::params($method) . ") {\n";
            $code .= '        return $this->handleMethodCall(\'';
            $code .= $methodName . '\', func_get_args());' . "\n    }\n";
        }

        $code .= "}\n";
        if ($class->inNamespace()) {
            $code .= "}\n";
        }

        if (false === eval($code)) {
            throw new \ReflectionException('Failure while creating CallMap instance of ' . $class->getName());
        }

        return new \ReflectionClass($class->getName() . 'CallMap');
    }

    /**
     * returns correct representation of parameters for given method
     *
     * @param   \ReflectionMethod  $method
     * @return  string
     */
    private static function params(\ReflectionMethod $method)
    {
        $params = '';
        foreach (parametersOf($method) as $name => $parameter) {
            if (strlen($params) > 0) {
                $params .= ', ';
            }

            /* @var $parameter \ReflectionParameter */
            if ($parameter->isArray()) {
                $params .= 'array ';
            } elseif ($parameter->getClass() !== null) {
                $params .= '\\' . $parameter->getClass()->getName() . ' ';
            } elseif ($parameter->isCallable()) {
                $params .= 'callable ';
            }

            $params .= '$' . $name;
            if ($parameter->allowsNull()) {
                $params .= ' = null';
            } elseif ($parameter->isOptional()) {
                $params .= ' = ' . ($parameter->isArray() ? '[]' : $parameter->getDefaultValue());
            }
        }

        return $params;
    }
}
