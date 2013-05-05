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
 * @deprecated  since 3.1.0, will be removed with 4.0.0
 */
class StringRepresentationBuilder
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
     * @param   mixed   $data        data to convert to a string
     * @param   array   $properties  the properties, if not set they will be retrieved
     * @return  string
     * @deprecated  since 3.1.0, will be removed with 4.0.0, use \net\stubbles\lang\__toString() instead
     */
    public static function buildFrom($data, array $properties = null)
    {
        return \net\stubbles\lang\__toString($data, $properties);
    }
}
?>