<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\exception;
use net\stubbles\lang\ClassLoader;
use net\stubbles\lang\Object;
use net\stubbles\lang\reflect\ReflectionObject;
/**
 * Base exception class for all stubbles runtime exceptions.
 *
 * A runtime exception should be thrown if a class is used in wrong way, e.g.
 * a missing configuration file or wrong class instance is supplied. Instances
 * of this and inherited exceptions should never be catched. The docblock of a
 * method must not indicate that a runtime exception may be thrown.
 */
class RuntimeException extends \RuntimeException implements Throwable
{
    /**
     * constructor
     *
     * @param  string  $message
     * @param  Exception  $cause
     * @param  int  $code
     */
    public function __construct($message, \Exception $cause = null, $code = 0)
    {
        parent::__construct($message, $code, $cause);
    }

    /**
     * returns class informations
     *
     * @return  ReflectionObject
     * @XmlIgnore
     */
    public function getClass()
    {
        return new ReflectionObject($this);
    }

    /**
     * returns the full qualified class name
     *
     * @return  string
     * @XmlIgnore
     */
    public function getClassName()
    {
        return get_class($this);
    }

    /**
     * returns a unique hash code for the class
     *
     * @return  string
     * @XmlIgnore
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
     * net\stubbles\lang\exception\RuntimeException {
     *     message(string): This is a runtime exception.
     *     file(string): foo.php
     *     line(integer): 4
     *     code(integer): 3
     *     stacktrace(string): __STACKTRACE__
     * }
     * [stack trace]
     * </code>
     *
     * @return  string
     * @XmlIgnore
     */
    public function __toString()
    {
        $string  = __CLASS__ . " {\n";
        $string .= '    message(string): ' . $this->getMessage() . "\n";
        $string .= '    file(string): ' . $this->getFile() . "\n";
        $string .= '    line(integer): ' . $this->getLine() . "\n";
        $string .= '    code(integer): ' . $this->getCode() . "\n";
        $string .= '    stacktrace(string): ' . $this->getTraceAsString() . "\n";
        $string .= "}\n";
        return $string;
    }
}
?>