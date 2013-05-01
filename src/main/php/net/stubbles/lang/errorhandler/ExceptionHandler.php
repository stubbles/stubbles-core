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
 * Interface for exception handlers.
 *
 * @api
 * @see  http://php.net/set_exception_handler
 */
interface ExceptionHandler
{
    /**
     * handles the exception
     *
     * @param  Exception  $exception  the uncatched exception
     */
    public function handleException(\Exception $exception);
}
?>