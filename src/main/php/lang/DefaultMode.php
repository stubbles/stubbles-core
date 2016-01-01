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
use stubbles\lang\errorhandler\DefaultErrorHandler;
use stubbles\lang\errorhandler\DisplayExceptionHandler;
/**
 * Handlings for different runtime modes of Stubbles.
 *
 * The mode instance contains information about which exception handler and
 * which error handler should be used, else well as whether caching is enabled
 * or not. Currently, there are four different default modes available:
 * <ul>
 * <li>DefaultMode::prod()
 *   <ul>
 *     <li>uses exception handler stubbles\lang\errorhandler\ProdModeExceptionHandler</li>
 *     <li>uses default error handler stubbles\lang\errorhandler\DefaultErrorHandler</li>
 *     <li>caching enabled</li>
 *   </ul>
 * </li>
 * <li>DefaultMode::test()
 *   <ul>
 *     <li>uses exception handler stubbles\lang\errorhandler\DisplayExceptionHandler</li>
 *     <li>uses default error handler stubbles\lang\errorhandler\DefaultErrorHandler</li>
 *     <li>caching enabled</li>
 *   </ul>
 * </li>
 * <li>DefaultMode::stage()
 *   <ul>
 *     <li>uses exception handler stubbles\lang\errorhandler\DisplayExceptionHandler</li>
 *     <li>no error handler</li>
 *     <li>caching disabled</li>
 *   </ul>
 * </li>
 * <li>DefaultMode::dev()
 *   <ul>
 *     <li>uses exception handler stubbles\lang\errorhandler\DisplayExceptionHandler</li>
 *     <li>no error handler</li>
 *     <li>caching disabled</li>
 *   </ul>
 * </li>
 * </ul>
 * While stage and dev mode currently are not different this may change in
 * future in case new mode depending switches become neccessary.
 *
 * To change the exception and/or error handler to be used, set the new ones
 * via setExceptionHandler()/setErrorHandler().
 * Please be aware that you still need to register the exception/error handler,
 * this is not done automatically, regardless whether you set your own ones or
 * not. Use registerExceptionHandler() and registerErrorHandler() to do so.
 *
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class DefaultMode implements Mode
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
    public static function create($modeName, $cacheEnabled)
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
     * @api
     * @return  \stubbles\Environment
     * @deprecated  since 7.0.0, use stubbles\environments\Production instead
     */
    public static function prod()
    {
        return new \stubbles\environments\Production();
    }

    /**
     * creates default test mode
     *
     * - exceptions will be displayed
     * - default error handling for PHP errors
     * - caching enabled
     *
     * @api
     * @return  Mode
     */
    public static function test()
    {
        return self::create('TEST', MODE::CACHE_ENABLED)
                   ->setExceptionHandler(DisplayExceptionHandler::class)
                   ->setErrorHandler(DefaultErrorHandler::class);
    }

    /**
     * creates default stage mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @api
     * @return  Mode
     */
    public static function stage()
    {
        return self::create('STAGE', MODE::CACHE_DISABLED)
                   ->setExceptionHandler(DisplayExceptionHandler::class);
    }

    /**
     * creates default dev mode
     *
     * - exceptions will be displayed
     * - no error handling for PHP errors
     * - caching disabled
     *
     * @api
     * @return  \stubbles\Environment
     * @deprecated  since 7.0.0, use stubbles\environments\Development instead
     */
    public static function dev()
    {
        return new \stubbles\environments\Development();
    }

    /**
     * returns the name of the mode
     *
     * @api
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
     * @param   string         $methodName  name of exception handler method
     * @return  \stubbles\lang\Mode
     * @throws  \InvalidArgumentException
     */
    public function setExceptionHandler($class, $methodName = 'handleException')
    {
        if (!is_string($class) && !is_object($class)) {
            throw new \InvalidArgumentException(
                    'Given class must be a class name or a class instance.'
            );
        }

        $this->exceptionHandler = ['class'  => $class,
                                   'method' => $methodName
                                  ];
        return $this;
    }

    /**
     * registers exception handler for current mode
     *
     * Return value depends on registration: if no exception handler set return
     * value will be false, if registered handler was an instance the handler
     * instance will be returned, and true in any other case.
     *
     * @param   string  $projectPath  path to project
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
     * @param   string|object  $class       name or instance of error handler class
     * @param   string         $methodName  name of error handler method
     * @return  \stubbles\lang\Mode
     * @throws  \InvalidArgumentException
     */
    public function setErrorHandler($class, $methodName = 'handle')
    {
        if (!is_string($class) && !is_object($class)) {
            throw new \InvalidArgumentException(
                    'Given class must be a class name or a class instance.'
            );
        }

        $this->errorHandler = ['class'  => $class,
                               'method' => $methodName
                              ];
        return $this;
    }

    /**
     * registers error handler for current mode
     *
     * Return value depends on registration: if no error handler set return value
     * will be false, if registered handler was an instance the handler instance
     * will be returned, and true in any other case.
     *
     * @param   string  $projectPath  path to project
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
     * checks whether cache is enabled or not
     *
     * @api
     * @return  bool
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * helper method to create the callback from the handler data
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @param   string         $projectPath  path to project
     * @return  callback
     */
    protected function createCallback($class, $methodName, $projectPath)
    {
        $instance = ((is_string($class)) ? (new $class($projectPath)) : ($class));
        return [$instance, $methodName];
    }
}
