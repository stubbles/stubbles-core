<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
/**
 * Class to build a string representation from an object.
 *
 * @internal
 */
class StringRepresentationBuilder extends BaseObject
{
    /**
     * returns a string representation of the class
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
     * @param   Object  $object      the object to convert to a string
     * @param   array   $properties  the properties, if not set they will be retrieved
     * @return  string
     */
    public static function buildFrom(Object $object, array $properties = null)
    {
        if (null === $properties) {
            $properties = ObjectParser::extractProperties($object);
        }

        $string = $object->getClassName() . " {\n";
        foreach ($properties as $name => $value) {
            $string .= '    ' . $name . '(' . self::determineType($value) . '): ';
            if (is_resource($value)) {
                $string .= "resource\n";
            } elseif (is_array($value)) {
                $string .= '[..](' .count($value). ")\n";
            } elseif (!($value instanceof Object)) {
                $string .= $value . "\n";
            } else {
                $string .= self::convertToStringRepresentation($value);
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
     */
    private static function determineType(&$value)
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_resource($value)) {
            return 'resource[' . get_resource_type($value) . ']';
        }

        return gettype($value);
    }

    /**
     * converts given value to string
     *
     * @param   mixed  $value
     * @return  string
     */
    private static function convertToStringRepresentation($value)
    {
        $string      = '';
        $lines       = explode("\n", (string) $value);
        $lineCounter = 0;
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            if (0 != $lineCounter) {
                $string .= '    ' . $line . "\n";
            } else {
                $string .= $line . "\n";
            }

            $lineCounter++;
        }

        return $string;
    }
}
?>