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
/**
 * Exception handler that displays the exception message nicely formated in the response.
 *
 * You should not use this exception handler in production mode!
 *
 * @internal
 */
class DisplayException extends AbstractExceptionHandler
{
    /**
     * creates response body with useful data for display
     *
     * @param   \Exception  $exception  the uncatched exception
     * @return  string
     */
    protected function createResponseBody(\Exception $exception)
    {
        return $exception->getMessage()
                . "\nTrace:\n" . $exception->getTraceAsString();
    }
}
