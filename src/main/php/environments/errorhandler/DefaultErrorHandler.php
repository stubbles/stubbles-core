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
 * Default collection of PHP error handlers.
 *
 * The collection consists of:
 *  - IllegalArgumentErrorHandler
 *      throws a InvalidArgumentException in case of an E_RECOVERABLE saying
 *      that a type hint was violated
 *  - LogErrorHandler
 *      logs all remaining errors into the logfile php-errors with log level error
 *
 * @internal
 */
class DefaultErrorHandler extends ErrorHandlers
{
    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->addErrorHandler(new InvalidArgument($projectPath));
        $this->addErrorHandler(new LogErrorHandler($projectPath));
    }
}
