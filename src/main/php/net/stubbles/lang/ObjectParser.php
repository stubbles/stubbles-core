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
 * Class to prase internal data from an object.
 *
 * @internal
 */
class ObjectParser extends BaseObject
{
    /**
     * helper method to extract all properties regardless of their visibility
     *
     * This is a workaround for the problem that as of PHP 5.2.4 get_object_vars()
     * is not any more capable of retrieving private properties from child classes.
     * See http://stubbles.org/archives/32-Subtle-BC-break-in-PHP-5.2.4.html.
     *
     * @param   object  $object
     * @return  array
     */
    public static function extractProperties($object)
    {
        $properties      = (array) $object;
        $fixedProperties = array();
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
     * reads a single property from given object
     *
     * @param   object  $object    object to read property from
     * @param   string  $property  name of property to read
     * @return  mixed  value of property
     */
    public static function readProperty($object, $property)
    {
        $properties = self::extractProperties($object);
        return $properties[$property];
    }
}
?>