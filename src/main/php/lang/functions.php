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
    use stubbles\lang\reflect\annotation\AnnotationCache;

    use function stubbles\values\typeOf;
    /**
     * shortcut for reflect($class, '__construct')
     *
     * @param   string|object  $class  class name of or object instance to reflect constructor of
     * @return  \ReflectionMethod
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
     * \ReflectionClass or \ReflectionObject which allows reflection on the
     * provided class.
     * In case $class is a function name it will return a
     * \ReflectionFunction which allows reflection on the
     * function.
     * In case a method name is provided it will return an instance of
     * \ReflectionMethod which allows reflection on the
     * specific method.
     *
     * @param   string|object  $class       class name, function name of or object instance to reflect
     * @param   string         $methodName  optional  specific method to reflect
     * @return  \ReflectionClass|\ReflectionMethod|\ReflectionFunction
     * @throws  \InvalidArgumentException
     * @since   3.1.0
     * @api
     */
    function reflect($class, $methodName = null)
    {
        if (is_array($class) && is_callable($class)) {
            return reflect($class[0], $class[1]);
        }

        if (null != $methodName) {
            return new \ReflectionMethod($class, $methodName);
        }

        if (is_string($class)) {
            if (class_exists($class) || interface_exists($class)) {
                return new \ReflectionClass($class);
            }

            return new \ReflectionFunction($class);
        }

        if (is_object($class)) {
            return new \ReflectionObject($class);
        }

        throw new \InvalidArgumentException(
                'Given class must either be a function name,'
                . ' class name or class instance, ' . typeOf($class) . ' given'
        );
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
     * @deprecated  since 7.0.0, will be removed with 8.0.0
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
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    function __toString($data, array $properties = null)
    {
        if (!is_object($data)) {
            return "{\n    (" . typeOf($data) . '): '. convertToStringRepresentation($data) . "}\n";
        }

        if (null === $properties) {
            $properties = extractObjectProperties($data);
        }

        $string = get_class($data) . " {\n";
        foreach ($properties as $name => $value) {
            $string .= '    ' . $name . '(' . typeOf($value) . '): ';
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
     * converts given value to string
     *
     * @param   mixed  $value
     * @return  string
     * @since   3.1.0
     * @deprecated  since 7.0.0, will be removed with 8.0.0
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
}
