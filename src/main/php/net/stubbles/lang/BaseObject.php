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
use net\stubbles\lang\reflect\ReflectionObject;
/**
 * Base class for all other stubbles classes except static ones and classes
 * extending php built-in classes.
 */
abstract class BaseObject implements Object
{
    /**
     * returns class informations
     *
     * @XmlIgnore
     * @return  ReflectionObject
     */
    public function getClass()
    {
        return new ReflectionObject($this);
    }

    /**
     * returns the full qualified class name
     *
     * @XmlIgnore
     * @return  string
     */
    public function getClassName()
    {
        return get_class($this);
    }

    /**
     * returns a unique hash code for the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof Object) {
            return ($this->hashCode() == $compare->hashCode());
        }

        return false;
    }

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
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return StringRepresentationBuilder::buildFrom($this);
    }
}
?>