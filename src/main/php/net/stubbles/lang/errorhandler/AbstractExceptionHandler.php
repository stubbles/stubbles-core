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
     * directory to log errors into
     *
     * @type  string
     */
    private $logDir;
    /**
     * mode for new directories
     *
     * @type  int
     */
    private $filemode       = 0700;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     * @param  string  $sapi         current php sapi
     */
    public function __construct($projectPath, $sapi = PHP_SAPI)
    {
        $this->projectPath = $projectPath;
        $this->sapi        = $sapi;
        $this->logDir      = $projectPath . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '{Y}' . DIRECTORY_SEPARATOR . '{M}';
    }

    /**
     * disables exception logging
     *
     * @return  AbstractExceptionHandler
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
     * @return  AbstractExceptionHandler
     */
    public function setFilemode($filemode)
    {
        $this->filemode = $filemode;
        return $this;
    }

    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public function handleException(\Exception $exception)
    {
        if (true === $this->loggingEnabled) {
            $this->log($exception);
        }

        if ('cgi' === $this->sapi) {
            $this->header('Status: 500 Internal Server Error');
        } else {
            $this->header('HTTP/1.1 500 Internal Server Error');
        }

        $this->writeBody($this->createResponseBody($exception));
    }

    /**
     * logs the exception into a logfile
     *
     * @param  Exception  $exception  the uncatched exception
     */
    protected function log(\Exception $exception)
    {
        $logData  = date('Y-m-d H:i:s');
        $logData .= '|' . get_class($exception);
        $logData .= '|' . $exception->getMessage();
        $logData .= '|' . $exception->getFile();
        $logData .= '|' . $exception->getLine();
        if (null !== $exception->getPrevious()) {
            $cause    = $exception->getPrevious();
            $logData .= '|' . get_class($cause);
            $logData .= '|' . $cause->getMessage();
            $logData .= '|' . $cause->getFile();
            $logData .= '|' . $cause->getLine();
        } else {
            $logData .= '||||';
        }

        $logDir = str_replace('{Y}', date('Y'), str_replace('{M}', date('m'), $this->logDir));
        if (!file_exists($logDir)) {
            mkdir($logDir, $this->filemode, true);
        }

        error_log($logData . "\n", 3, $logDir . DIRECTORY_SEPARATOR . 'exceptions-' . date('Y-m-d') . '.log');
    }

    /**
     * creates response body with useful data for display
     *
     * @param   Exception     $exception  the uncatched exception
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