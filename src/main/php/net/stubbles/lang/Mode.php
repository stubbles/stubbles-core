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
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not.
 */
interface Mode
{
    /**
     * constant for enabled cache
     */
    const CACHE_ENABLED  = true;
    /**
     * constant for disabled cache
     */
    const CACHE_DISABLED = false;
    /**
     * returns the name of the mode
     *
     * @api
     * @return  string
     */
    public function name();

    /**
     * sets the exception handler to given class and method name
     *
     * To register the new exception handler call registerExceptionHandler().
     *
     * @param   string|object  $class        name or instance of exception handler class
     * @param   string         $methodName   name of exception handler method
     * @return  Mode
     */
    public function setExceptionHandler($class, $methodName);

    /**
     * registers exception handler for current mode
     *
     * Return value depends on registration: if no exception handler set return
     * value will be false, if registered handler was an instance the handler
     * instance will be returned, and true in any other case.
     *
     * @param   string       $projectPath  path to project
     * @return  bool|object
     */
    public function registerExceptionHandler($projectPath);

    /**
     * sets the error handler to given class and method name
     *
     * To register the new error handler call registerErrorHandler().
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @return  Mode
     */
    public function setErrorHandler($class, $methodName);

    /**
     * registers error handler for current mode
     *
     * Return value depends on registration: if no error handler set return value
     * will be false, if registered handler was an instance the handler instance
     * will be returned, and true in any other case.
     *
     * @param   string       $projectPath  path to project
     * @return  bool|object
     */
    public function registerErrorHandler($projectPath);

    /**
     * checks whether cache is enabled or not
     *
     * @api
     * @return  bool
     */
    public function isCacheEnabled();
}
?>