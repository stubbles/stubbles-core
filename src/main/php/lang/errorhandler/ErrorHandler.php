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
use stubbles\environments\ErrorHandler as NewErrorHandler;
/**
 * Interface for PHP error handlers.
 *
 * @api
 * @see  http://php.net/set_error_handler
 * @deprecated  since 7.0.0, use will be removed with 8.0.0
 */
interface ErrorHandler extends NewErrorHandler
{
    // intentionally empty
}
