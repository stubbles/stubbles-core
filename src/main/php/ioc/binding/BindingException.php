<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
/**
 * Exception to be thrown in case a binding is invalid or missing
 */
class BindingException extends \Exception
{
    /**
     * constructor
     *
     * @param  string                    $message
     * @param  \stubbles\peer\Exception  $previous
     */
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
