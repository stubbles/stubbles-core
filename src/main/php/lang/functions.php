<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang {
    use stubbles\lang\exception\IllegalArgumentException;
    use stubbles\lang\Properties;
    use stubbles\lang\reflect\MixedType;
    use stubbles\lang\reflect\ReflectionClass;
    use stubbles\lang\reflect\ReflectionFunction;
    use stubbles\lang\reflect\ReflectionMethod;
    use stubbles\lang\reflect\ReflectionObject;
    use stubbles\lang\reflect\ReflectionPrimitive;
    use stubbles\lang\reflect\annotation\AnnotationCache;

    /**
     * creates new properties instance from given property data
     *
     * @param   array  $propertyData
     * @return  Properties
     * @since   3.1.0
     * @api
     */
    function properties(array $propertyData = [])
    {
        return new Properties($propertyData);
    }

    /**
     * parses given property string and returns properties instance
     *
     * @param   string  $propertyString
     * @return  Properties
     * @since   3.1.0
     * @api
     */
    function parseProperties($propertyString)
    {
        return Properties::fromString($propertyString);
    }

    /**
     * parses properties from given file and returns properties instance
     *
     * @param   string  $propertiesFile
     * @return  Properties
     * @since   3.1.0
     * @api
     */
    function parsePropertiesFile($propertiesFile)
    {
        return Properties::fromFile($propertiesFile);
    }

    /**
     * shortcut for reflect($class, '__construct')
     *
     * @param   string|object  $class  class name of or object instance to reflect constructor of
     * @return  lang\reflect\ReflectionMethod
     * @since   3.1.0
     * @api
     */
    function reflectConstructor($class)
    {
        return reflect($class, '__construct');
    }

    /**
     * reflects given input and returns an appropriate reflector
     *
     * If no method name is provided it will check whether $class denotes a
     * class name or is an object instance. If yes it returns an instance of
     * stubbles\lang\reflect\BaseReflectionClass which allows reflection on the
     * provided class.
     * In case $class is a function name it will return a
     * stubbles\lang\reflect\ReflectionFunction which allows reflection on the
     * function.
     * In case a method name is provided it will return an instance of
     * stubbles\lang\reflect\ReflectionMethod which allows reflection on the
     * specific method.
     *
     * @param   string|object  $class       class name, function name of or object instance to reflect
     * @param   string         $methodName  optional  specific method to reflect
     * @return  stubbles\lang\reflect\BaseReflectionClass|stubbles\lang\reflect\ReflectionMethod|stubbles\lang\reflect\ReflectionFunction
     * @throws  IllegalArgumentException
     * @since   3.1.0
     * @api
     */
    function reflect($class, $methodName = null)
    {
        if (null != $methodName) {
            return new ReflectionMethod($class, $methodName);
        }

        if (is_string($class)) {
            if (class_exists($class) || interface_exists($class)) {
                return ReflectionClass::fromName($class);
            }

            return new ReflectionFunction($class);
        }

        if (is_object($class)) {
            return ReflectionObject::fromInstance($class);
        }

        throw new IllegalArgumentException('Given class must either be a function name, class name or class instance, ' . gettype($class) . ' given');
    }

    /**
     * returns a type instance for given type name
     *
     * @param   string  $typeName
     * @return  \stubbles\lang\reflect\ReflectionType
     * @since   3.1.1
     */
    function typeFor($typeName)
    {
        if (ReflectionPrimitive::isKnown($typeName)) {
            return ReflectionPrimitive::forName($typeName);
        } elseif (MixedType::isKnown($typeName)) {
            return MixedType::forName($typeName);
        }

        return new ReflectionClass($typeName);
    }

    /**
     * enable persistent annotation cache with given cache storage logic
     *
     * The $readCache closure must return the stored annotation data. If no such
     * data is present it must return null. In case the stored annotation data
     * can't be unserialized into an array a
     * stubbles\lang\exception\RuntimeException will be thrown.
     *
     * The $storeCache closure must store passed annotation data. It doesn't
     * need to take care about serialization, as it already receives a
     * serialized representation.
     *
     * A possible implementation for the file cache would look like this:
     * <code>
     * self::persistAnnotations(function() use($cacheFile)
     *                          {
     *                              if (file_exists($cacheFile)) {
     *                                  return file_get_contents($cacheFile);
     *                              }
     *
     *                              return null;
     *                          },
     *                          function($annotationData) use($cacheFile)
     *                          {
     *                              file_put_contents($cacheFile, $annotationData);
     *                          }
     * );
     * </code>
     *
     * @param  \Closure  $readCache
     * @param  \Closure  $storeCache
     * @since  3.1.0
     * @api
     */
    function persistAnnotations(\Closure $readCache, \Closure $storeCache)
    {
        AnnotationCache::start($readCache, $storeCache);
    }

    /**
     * enable persistent annotation cache by telling where to store cache data
     *
     * @param  string  $cacheFile
     * @since  3.1.0
     * @api
     */
    function persistAnnotationsInFile($cacheFile)
    {
        AnnotationCache::startFromFileCache($cacheFile);
    }

    /**
     * method to extract all properties regardless of their visibility
     *
     * This is a workaround for the problem that as of PHP 5.2.4 get_object_vars()
     * is not any more capable of retrieving private properties from child classes.
     * See http://stubbles.org/archives/32-Subtle-BC-break-in-PHP-5.2.4.html.
     *
     * @param   object  $object
     * @return  array
     * @since   3.1.0
     */
    function extractObjectProperties($object)
    {
        $properties      = (array) $object;
        $fixedProperties = [];
        foreach ($properties as $propertyName => $propertyValue) {
            if (!strstr($propertyName, "\0")) {
                $fixedProperties[$propertyName] = $propertyValue;
                continue;
            }

            $fixedProperties[substr($propertyName, strrpos($propertyName, "\0") + 1)] = $propertyValue;
        }

        return $fixedProperties;
    }

    /**
     * returns a string representation of given data
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example\MyClass {
     *     foo(string): hello
     *     bar(example\AnotherClass): example\AnotherClass {
     *         baz(int): 5
     *     }
     * }
     * </code>
     * Please note that protected and private properties of the class wil only be
     * in the result if the second argument contains a list of the properties and
     * its values. If not set only public properties can be extracted due to the
     * behaviour of get_object_vars().
     *
     * @param   mixed   $data        data to convert to a string
     * @param   array   $properties  the properties, if not set they will be retrieved
     * @return  string
     * @since   3.1.0
     * @api
     */
    function __toString($data, array $properties = null)
    {
        if (!is_object($data)) {
            return "{\n    (" . getType($data) . '): '. convertToStringRepresentation($data) . "}\n";
        }

        if (null === $properties) {
            $properties = extractObjectProperties($data);
        }

        $string = get_class($data) . " {\n";
        foreach ($properties as $name => $value) {
            $string .= '    ' . $name . '(' . getType($value) . '): ';
            if (is_resource($value)) {
                $string .= "resource\n";
            } elseif (is_array($value)) {
                $string .= '[..](' . count($value) . ")\n";
            } else {
                $string .= convertToStringRepresentation($value);
            }
        }

        $string .= "}\n";
        return $string;
    }

    /**
     * determines the correct type of a value
     *
     * @param   mixed   &$value
     * @return  string
     * @since   3.1.0
     */
    function getType(&$value)
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_resource($value)) {
            return 'resource[' . get_resource_type($value) . ']';
        }

        return \gettype($value);
    }

    /**
     * converts given value to string
     *
     * @param   mixed  $value
     * @return  string
     * @since   3.1.0
     */
    function convertToStringRepresentation($value)
    {
        $string = '';
        $lines = explode("\n", (string) $value);
        foreach ($lines as $lineCounter => $line) {
            if (empty($line)) {
                continue;
            }

            if (0 != $lineCounter) {
                $string .= '    ' . $line . "\n";
            } else {
                $string .= $line . "\n";
            }
        }

        return $string;
    }

    /**
     * ensures that the given callable is really callable
     *
     * Internal functions like strlen() or is_string() require an *exact* amount
     * of arguments. If they receive too many arguments they just report an error.
     * This is a problem when you pass such a function as callable to a place
     * where it will receive more than it's exact amount of arguments.
     *
     * @param   callable  $callable
     * @return  callable
     * @since   4.0.0
     */
    function ensureCallable(callable $callable)
    {
        if (!is_string($callable)) {
            return $callable;
        }

        $function = reflect($callable);
        if ($function->isInternal()) {
            return reflect($callable)->wrapInClosure();
        }

        return $callable;
    }
}
namespace stubbles\lang\exception {
    /**
     * returns error message from last error that occurred
     *
     * @return  string
     * @since   3.4.2
     */
    function lastErrorMessage()
    {
        $error = error_get_last();
        if (null === $error) {
            return null;
        }

        return $error['message'];
    }
}
