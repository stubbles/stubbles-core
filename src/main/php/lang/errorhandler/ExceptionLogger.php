<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\errorhandler;
/**
 * Abstract base implementation for exception handlers, containing logging of exceptions.
 *
 * @since  3.3.0
 * @Singleton
 */
class ExceptionLogger
{
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
    private $filemode = 0700;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     * @Inject
     * @Named('stubbles.project.path')
     */
    public function __construct($projectPath)
    {
        $this->logDir = $projectPath
                . DIRECTORY_SEPARATOR
                . 'log'
                . DIRECTORY_SEPARATOR
                . 'errors'
                . DIRECTORY_SEPARATOR
                . '{Y}'
                . DIRECTORY_SEPARATOR
                . '{M}';
    }

    /**
     * sets the mode for new log directories
     *
     * @param   int  $filemode
     * @return  \stubbles\lang\errorhandler\ExceptionLogger
     */
    public function setFilemode($filemode)
    {
        $this->filemode = $filemode;
        return $this;
    }

    /**
     * logs the exception into a logfile
     *
     * @param  \Exception  $exception  exception to log
     */
    public function log(\Exception $exception)
    {
        $logData  = date('Y-m-d H:i:s');
        $logData .= $this->exceptionFields($exception);
        $logData .= $this->fieldsForPrevious($exception->getPrevious());
        error_log(
                $logData . "\n",
                3,
                $this->getLogDir() . DIRECTORY_SEPARATOR . 'exceptions-' . date('Y-m-d') . '.log'
        );
    }

    /**
     * returns fields for exception to log
     *
     * @param   \Exception  $exception
     * @return  string
     */
    private function exceptionFields(\Exception $exception)
    {
        return '|' . get_class($exception)
             . '|' . $exception->getMessage()
             . '|' . $exception->getFile()
             . '|' . $exception->getLine();
    }

    /**
     * returns fields for previous exception
     *
     * @param   \Exception  $exception
     * @return  string
     */
    private function fieldsForPrevious(\Exception $exception = null)
    {
        if (null === $exception) {
            return '||||';
        }

        return $this->exceptionFields($exception);
    }

    /**
     * returns directory where to write logfile to
     *
     * @return  string
     */
    private function getLogDir()
    {
        $logDir = str_replace(
                '{Y}',
                date('Y'),
                str_replace('{M}', date('m'), $this->logDir)
        );
        if (!file_exists($logDir)) {
            mkdir($logDir, $this->filemode, true);
        }

        return $logDir;
    }
}
