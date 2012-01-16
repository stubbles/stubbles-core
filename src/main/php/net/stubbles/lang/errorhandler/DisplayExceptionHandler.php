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
use net\stubbles\lang\exception\Throwable;
/**
 * Exception handler that displays the exception message nicely formated in the response.
 *
 * You should not use this exception handler in production mode!
 */
class DisplayExceptionHandler extends AbstractExceptionHandler
{
    /**
     * creates response body with useful data for display
     *
     * @param   \Exception     $exception  the uncatched exception
     * @return  string
     */
    protected function createResponseBody(\Exception $exception)
    {
        $body = '';
        if ($exception instanceof Throwable) {
            $body .= (string) $exception;
        } else {
            $body .= $exception->getMessage();
        }

        return $body . "\nTrace:\n" . $exception->getTraceAsString();
    }
}