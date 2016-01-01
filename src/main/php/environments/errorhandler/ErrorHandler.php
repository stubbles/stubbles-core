<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments\errorhandler;
/**
 * Interface for PHP error handlers.
 *
 * @see  http://php.net/set_error_handler
 */
interface ErrorHandler
{
    /**
     * constant to signal that php's internal error handling should take over
     */
    const CONTINUE_WITH_PHP_INTERNAL_HANDLING = false;
    /**
     * constant to signal error handling should be stopped
     */
    const STOP_ERROR_HANDLING                 = true;

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
    public function isResponsible($level, $message, $file = null, $line = null, array $context = []);

    /**
     * checks whether this error is supressable
     *
     * This method is called in case the level is 0. It decides whether the
     * error has to be handled or if it can be omitted.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = []);

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     */
    public function handle($level, $message, $file = null, $line = null, array $context = []);
}
