<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments;
/**
 * Provides methods for error and exception handling in environments.
 *
 * The main reason for the trait is that stubbles\Environment must be an
 * interface to maintain backwards compatibility.
 */
trait Handler
{
    /**
     * exception handler to be used in the mode
     *
     * @type  array
     */
    private $exceptionHandler = null;
    /**
     * error handler to be used in the mode
     *
     * @type  array
     */
    private $errorHandler     = null;

    /**
     * sets the exception handler to given class and method name
     *
     * To register the new exception handler call registerExceptionHandler().
     *
     * @param   string|object  $class        name or instance of exception handler class
     * @param   string         $methodName   name of exception handler method
     * @return  \stubbles\Environment
     */
    protected function setExceptionHandler($class, $methodName = 'handleException')
    {
        if (!is_string($class) && !is_object($class)) {
            throw new \InvalidArgumentException(
                    'Given class must be a class name or a class instance.'
            );
        }

        $this->exceptionHandler = ['class' => $class, 'method' => $methodName];
        return $this;
    }

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
    public function registerExceptionHandler($projectPath)
    {
        if (null === $this->exceptionHandler) {
            return false;
        }

        $callback = $this->createCallback(
                $this->exceptionHandler['class'],
                $this->exceptionHandler['method'],
                $projectPath
        );
        set_exception_handler($callback);
        return $callback[0];
    }

    /**
     * sets the error handler to given class and method name
     *
     * To register the new error handler call registerErrorHandler().
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @return  \stubbles\Environment
     */
    protected function setErrorHandler($class, $methodName = 'handle')
    {
        if (!is_string($class) && !is_object($class)) {
            throw new \InvalidArgumentException(
                    'Given class must be a class name or a class instance.'
            );
        }

        $this->errorHandler = ['class' => $class, 'method' => $methodName];
        return $this;
    }

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
    public function registerErrorHandler($projectPath)
    {
        if (null === $this->errorHandler) {
            return false;
        }

        $callback = $this->createCallback(
                $this->errorHandler['class'],
                $this->errorHandler['method'],
                $projectPath
        );
        set_error_handler($callback);
        return $callback[0];
    }

    /**
     * helper method to create the callback from the handler data
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @param   string         $projectPath  path to project
     * @return  callback
     */
    private function createCallback($class, $methodName, $projectPath)
    {
        $instance = ((is_string($class)) ? (new $class($projectPath)) : ($class));
        return [$instance, $methodName];
    }
}
