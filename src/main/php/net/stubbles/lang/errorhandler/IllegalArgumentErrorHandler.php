<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\errorhandler;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Error handler for illegal arguments.
 *
 * This error handler is responsible for errors of type E_RECOVERABLE_ERROR which denote that
 * a type hint has been infringed with an argument of another type. If such an error is detected
 * an stubIllegalArgumentException will be thrown.
 *
 * @see  http://php.net/set_error_handler
 */
class IllegalArgumentErrorHandler extends BaseObject implements ErrorHandler
{
    /**
     * checks whether this error handler is responsible for the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error handler is responsible, else false
     */
    public function isResponsible($level, $message, $file = null, $line = null, array $context = array())
    {
        if (E_RECOVERABLE_ERROR != $level) {
            return false;
        }

        return (bool) preg_match('/Argument [0-9]+ passed to [a-zA-Z0-9_\\\\]+::[a-zA-Z0-9_]+\(\) must be an instance of [a-zA-Z0-9_\\\\]+, [a-zA-Z0-9_\\\\]+ given/', $message);
    }

    /**
     * checks whether this error is supressable
     *
     * This method is called in case the level is 0. A type hint infringement
     * is never supressable.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = array())
    {
        return false;
    }

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     * @throws  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function handle($level, $message, $file = null, $line = null, array $context = array())
    {
        throw new IllegalArgumentException($message . ' @ ' . $file . ' on line ' . $line);
    }
}
?>