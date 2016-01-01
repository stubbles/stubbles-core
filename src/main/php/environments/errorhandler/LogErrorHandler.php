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
 * Error handler that logs all errors.
 *
 * This error handler logs all errors that occured. In a composition of error
 * handlers it should be added as last one so it catches all errors that have
 * not been handled before.
 *
 * @internal
 */
class LogErrorHandler implements ErrorHandler
{
    /**
     * list of error levels and their string representation
     *
     * @type  string[]
     */
    private static $levelStrings  = [E_ERROR             => 'E_ERROR',
                                     E_WARNING           => 'E_WARNING',
                                     E_PARSE             => 'E_PARSE',
                                     E_NOTICE            => 'E_NOTICE',
                                     E_CORE_ERROR        => 'E_CORE_ERROR',
                                     E_CORE_WARNING      => 'E_CORE_WARNING',
                                     E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
                                     E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
                                     E_USER_ERROR        => 'E_USER_ERROR',
                                     E_USER_WARNING      => 'E_USER_WARNING',
                                     E_USER_NOTICE       => 'E_USER_NOTICE',
                                     E_STRICT            => 'E_STRICT',
                                     E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                                     E_ALL               => 'E_ALL'
                                    ];
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
    private $filemode  = 0700;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->logDir = $projectPath . DIRECTORY_SEPARATOR . 'log'
                . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR
                . '{Y}' . DIRECTORY_SEPARATOR . '{M}';
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int  $filemode
     * @return  \stubbles\lang\errorhandler\LogErrorHandler
     */
    public function setFilemode($filemode)
    {
        $this->filemode = $filemode;
        return $this;
    }

    /**
     * checks whether this error handler is responsible for the given error
     *
     * This error handler is always responsible.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true
     */
    public function isResponsible($level, $message, $file = null, $line = null, array $context = [])
    {
        return true;
    }

    /**
     * checks whether this error is supressable
     *
     * This method is called in case the level is 0. An error to log is never
     * supressable.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     filename that the error was raised in
     * @param   int     $line     line number the error was raised at
     * @param   array   $context  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = [])
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
     */
    public function handle($level, $message, $file = null, $line = null, array $context = [])
    {
        $logData  = date('Y-m-d H:i:s') . '|' . $level;
        $logData .= '|' . ((isset(self::$levelStrings[$level])) ? (self::$levelStrings[$level]) : ('unknown'));
        $logData .= '|' . $message;
        $logData .= '|' . $file;
        $logData .= '|' . $line;
        $logDir   = $this->buildLogDir();
        if (!file_exists($logDir)) {
            mkdir($logDir, $this->filemode, true);
        }

        error_log(
                $logData . "\n",
                3,
                $logDir . DIRECTORY_SEPARATOR . 'php-error-' . date('Y-m-d') . '.log'
        );
        return ErrorHandler::STOP_ERROR_HANDLING;
    }

    /**
     * builds the log directory
     *
     * @return  string
     */
    private function buildLogDir()
    {
        return str_replace('{Y}', date('Y'), str_replace('{M}', date('m'), $this->logDir));
    }

}
