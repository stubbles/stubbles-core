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
use stubbles\environments\ExceptionLogger as NewExceptionLogger;
/**
 * Can be used to log exceptions.
 *
 * @since  3.3.0
 * @Singleton
 * @deprecated  since 7.0.0, use stubbles\environments\ExceptionLogger instead, will be removed with 8.0.0
 */
class ExceptionLogger extends NewExceptionLogger
{
   // intentionally empty
}
