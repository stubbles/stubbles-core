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
 * An IllegalAccessException is thrown when an object is accessed in a way for which no permissions exist.
 *
 * @deprecated  since 5.0.0, use LogicException instead, will be removed with 6.0.0
 */
class IllegalAccessException extends \LogicException implements Throwable
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
     * example\MyException {
     *     message(string): This is an exception.
     *     file(string): foo.php
     *     line(integer): 4
     *     code(integer): 3
     *     stacktrace(string): __STACKTRACE__
     * }
     * </code>
     *
     * @return  string
     * @XmlIgnore
     */
    public function __toString()
    {
        $string  = get_class($this) . " {\n";
        $string .= '    message(string): ' . $this->getMessage() . "\n";
        $string .= '    file(string): ' . $this->getFile() . "\n";
        $string .= '    line(integer): ' . $this->getLine() . "\n";
        $string .= '    code(integer): ' . $this->getCode() . "\n";
        $string .= '    stacktrace(string): ' . $this->getTraceAsString() . "\n";
        $string .= "}\n";
        return $string;
    }
}
