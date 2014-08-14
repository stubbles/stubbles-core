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
 * Exception to be thrown in case an illegal argument was given.
 */
class IllegalArgumentException extends \InvalidArgumentException implements Throwable
{
    /**
     * name of affected parameter
     *
     * @type  string
     */
    private $paramName;
    /**
     * illegal value that was passed for the affected parameter
     *
     * @type  mixed
     */
    private $value;

    /**
     * constructor
     *
     * @param  string      $message
     * @param  string      $paramName  name of affected parameter
     * @param  mixed       $value      illegal value that was passed for the affected parameter
     * @param  \Exception  $cause
     * @param  int         $code
     */
    public function __construct($message, $paramName = null, $value = null, \Exception $cause = null, $code = 0)
    {
        parent::__construct($message, $code, $cause);
        $this->paramName = $paramName;
        $this->value     = $value;
    }

    /**
     * checks whether any details are known
     *
     * @return  bool
     */
    public function hasDetails()
    {
        return null !== $this->paramName;
    }

    /**
     * returns name of affected parameter
     *
     * @return  string
     * @since   2.0.0
     */
    public function getAffectedParamName()
    {
        return $this->paramName;
    }

    /**
     * returns illegal value that was passed for the affected parameter
     *
     * @return  mixed
     * @since   2.0.0
     */
    public function getIllegalParamValue()
    {
        return $this->value;
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
