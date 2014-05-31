<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
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
     * @param  string      $message
     * @param  \Exception  $cause
     * @param  int         $code
     */
    public function __construct($message, \Exception $cause = null, $code = 0)
    {
        parent::__construct($message, $code, $cause);
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * stubbles\lang\exception\RuntimeException {
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
