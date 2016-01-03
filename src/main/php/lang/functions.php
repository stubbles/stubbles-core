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

    use function stubbles\values\typeOf;

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
