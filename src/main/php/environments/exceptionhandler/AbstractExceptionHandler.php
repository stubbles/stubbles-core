<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments\exceptionhandler;
use stubbles\environments\ExceptionHandler;
use stubbles\environments\ExceptionLogger;
/**
 * Abstract base implementation for exception handlers, containing logging of exceptions.
 *
 * @internal
 */
abstract class AbstractExceptionHandler implements ExceptionHandler
{
    /**
     * path to project
     *
     * @type  string
     */
    protected $projectPath;
    /**
     * current php sapi
     *
     * @type  string
     */
    private $sapi;
    /**
     * switch whether logging is enabled or not
     *
     * @type  bool
     */
    private $loggingEnabled = true;
    /**
     * logger for exceptions
     *
     * @type  \stubbles\environments\ExceptionLogger
     */
    private $exceptionLogger;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     * @param  string  $sapi         current php sapi
     */
    public function __construct($projectPath, $sapi = PHP_SAPI)
    {
        $this->projectPath     = $projectPath;
        $this->sapi            = $sapi;
        $this->exceptionLogger = new ExceptionLogger($projectPath);
    }

    /**
     * disables exception logging
     *
     * @return  \stubbles\lang\errorhandler\AbstractExceptionHandler
     */
    public function disableLogging()
    {
        $this->loggingEnabled = false;
        return $this;
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int  $filemode
     * @return  \stubbles\lang\errorhandler\AbstractExceptionHandler
     */
    public function setFilemode($filemode)
    {
        $this->exceptionLogger->setFilemode($filemode);
        return $this;
    }

    /**
     * handles the exception
     *
     * @param  \Exception  $exception  the uncatched exception
     */
    public function handleException(\Exception $exception)
    {
        if ($this->loggingEnabled) {
            $this->exceptionLogger->log($exception);
        }

        if ('cgi' === $this->sapi) {
            $this->header('Status: 500 Internal Server Error');
        } else {
            $this->header('HTTP/1.1 500 Internal Server Error');
        }

        $this->writeBody($this->createResponseBody($exception));
    }

    /**
     * creates response body with useful data for display
     *
     * @param   \Exception  $exception  the uncatched exception
     * @return  string
     */
    protected abstract function createResponseBody(\Exception $exception);

    /**
     * helper method to send the header
     *
     * @param  string  $header
     */
    protected function header($header)
    {
        header($header);
    }

    /**
     * helper method to send the body
     *
     * @param  string  $body
     */
    protected function writeBody($body)
    {
        echo $body;
        flush();
    }
}
