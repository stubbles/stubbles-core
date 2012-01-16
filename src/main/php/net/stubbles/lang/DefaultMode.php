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
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not. Currently, there are four different default modes available:
 * DefaultMode::prod()
 *      - uses exception handler net\stubbles\lang\errorhandler\ProdModeExceptionHandler
 *      - uses default error handler net\stubbles\lang\errorhandler\DefaultErrorHandler
 *      - caching enabled
 * DefaultMode::test()
 *      - uses exception handler net\stubbles\lang\errorhandler\DisplayExceptionHandler
 *      - uses default error handler net\stubbles\lang\errorhandler\DefaultErrorHandler
 *      - caching enabled
 * DefaultMode::stage()
 *      - uses exception handler net\stubbles\lang\errorhandler\DisplayExceptionHandler
 *      - no error handler
 *      - caching disabled
 * DefaultMode::dev()
 *      - uses exception handler net\stubbles\lang\errorhandler\DisplayExceptionHandler
 *      - no error handler
 *      - caching disabled
 * While stage and dev mode currently are not different this may change in
 * future in case new mode depending switches become neccessary.
 *
 * To change the exception and/or error handler to be used, set the new ones
 * via setExceptionHandler()/setErrorHandler().
 * Please be aware that you still need to register the exception/error handler,
 * this is not done automatically, regardless whether you set your own ones or
 * not. Use registerExceptionHandler() and registerErrorHandler() to do so.
 */
class DefaultMode extends BaseObject implements Mode
{
    /**
     * name of mode
     *
     * @type  string
     */
    protected $modeName;
    /**
     * exception handler to be used in the mode
     *
     * @type  array
     */
    protected $exceptionHandler = null;
    /**
     * error handler to be used in the mode
     *
     * @type  array
     */
    protected $errorHandler     = null;
    /**
     * switch whether cache should be enabled in mode or not
     *
     * @type  bool
     */
    protected $cacheEnabled     = true;

    /**
     * constructor
     *
     * Use this to create your own mode. However you might want to use one of
     * the four default modes delivered by this class, see below for the static
     * constructor methods prod(), test(), stage() and dev().
     *
     * @param  string  $modeName
     * @param  bool    $cacheEnabled
     */
    public function __construct($modeName, $cacheEnabled)
    {
        $this->modeName     = $modeName;
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * static constructor
     *
     * @param   string  $modeName
     * @param   bool    $cacheEnabled
     * @return  Mode
     */
    public static function newInstance($modeName, $cacheEnabled)
    {
        return new self($modeName, $cacheEnabled);
    }

    /**
     * creates default production mode
     *
     * - exceptions will be logged, error 500 will be shown instead of exception
     * - default error handling for PHP errors
     * - caching enabled
     *
     * @return  Mode
     */
    public static function prod()
    {
        return self::newInstance('PROD', Mode::CACHE_ENABLED)
                   ->setExceptionHandler('net\\stubbles\\lang\\errorhandler\\ProdModeExceptionHandler')
                   ->setErrorHandler('net\\stubbles\\lang\\errorhandler\\DefaultErrorHandler');
    }

    /**
     * creates default test mode
     *
     * - exceptions will be displayed
     * - default error handling for PHP errors
     * - caching enabled
     *
     * @return  Mode
     */
    public static function test()
    {
        return self::newInstance('TEST', MODE::CACHE_ENABLED)
                   ->setExceptionHandler('net\\stubbles\\lang\\errorhandler\\DisplayExceptionHandler')
                   ->setErrorHandler('net\\stubbles\\lang\\errorhandler\\DefaultErrorHandler');
    }

    /**
     * creates default stage mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @return  Mode
     */
    public static function stage()
    {
        return self::newInstance('STAGE', MODE::CACHE_DISABLED)
                   ->setExceptionHandler('net\\stubbles\\lang\\errorhandler\\DisplayExceptionHandler');
    }

    /**
     * creates default dev mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @return  Mode
     */
    public static function dev()
    {
        return self::newInstance('DEV', MODE::CACHE_DISABLED)
                   ->setExceptionHandler('net\\stubbles\\lang\\errorhandler\\DisplayExceptionHandler');
    }

    /**
     * returns the name of the mode
     *
     * @return  string
     */
    public function name()
    {
        return $this->modeName;
    }

    /**
     * sets the exception handler to given class and method name
     *
     * To register the new exception handler call registerExceptionHandler().
     *
     * @param   string|object  $class       name or instance of exception handler class
     * @param   string         $methodName  optional  name of exception handler method
     * @return  Mode
     * @throws  IllegalArgumentException
     */
    public function setExceptionHandler($class, $methodName = 'handleException')
    {
        if (is_string($class) === false && is_object($class) === false) {
            throw new IllegalArgumentException('Given class must be a class name or a class instance.');
        }

        $this->exceptionHandler = array('class'  => $class,
                                        'method' => $methodName
                                  );
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

        $callback = $this->createCallback($this->exceptionHandler['class'],
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
     * @param   string|object  $class       name or instance of error handler class
     * @param   string         $methodName  optional name of error handler method
     * @return  Mode
     * @throws  IllegalArgumentException
     */
    public function setErrorHandler($class, $methodName = 'handle')
    {
        if (is_string($class) === false && is_object($class) === false) {
            throw new IllegalArgumentException('Given class must be a class name or a class instance.');
        }

        $this->errorHandler = array('class'  => $class,
                                    'method' => $methodName
                              );
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

        $callback = $this->createCallback($this->errorHandler['class'],
                                          $this->errorHandler['method'],
                                          $projectPath
                    );
        set_error_handler($callback);
        return $callback[0];
    }

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * helper method to create the callback from the handler data
     *
     * @param   string|object  $class       name or instance of error handler class
     * @param   string         $methodName  optional name of error handler method
     * @param   string         $projectPath  path to project
     * @return  callback
     */
    protected function createCallback($class, $methodName, $projectPath)
    {
        $instance = ((is_string($class)) ? (new $class($projectPath)) : ($class));
        return array($instance, $methodName);
    }
}
?>