<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\Environment;
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not.
 *
 * @deprecated  since 7.0.0, use stubbles\Environment, will be removed with 8.0.0
 */
interface Mode extends Environment
{
    /**
     * sets the exception handler to given class and method name
     *
     * To register the new exception handler call registerExceptionHandler().
     *
     * @param   string|object  $class        name or instance of exception handler class
     * @param   string         $methodName   name of exception handler method
     * @return  \stubbles\lang\Mode
     */
    public function setExceptionHandler($class, $methodName);

    /**
     * sets the error handler to given class and method name
     *
     * To register the new error handler call registerErrorHandler().
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @return  \stubbles\lang\Mode
     */
    public function setErrorHandler($class, $methodName);
}
