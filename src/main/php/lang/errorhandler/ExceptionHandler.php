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
use stubbles\environments\exceptionhandler\ExceptionHandler as NewExceptionHandler;
/**
 * Interface for exception handlers.
 *
 * @api
 * @see  http://php.net/set_exception_handler
 * @deprecated  since 7.0.0, use will be removed with 8.0.0
 */
interface ExceptionHandler extends NewExceptionHandler
{
    // intentionally empty
}
