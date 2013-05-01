<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\exception;
/**
 * Exception to be thrown in case an illegal argument was given.
 */
class IllegalArgumentException extends Exception
{
    /**
     * name of affected parameter
     *
     * @type  string
     */
    private $paramName;
    /**
     * illegal value that was passed for the affected parameter
     *
     * @type  mixed
     */
    private $value;

    /**
     * constructor
     *
     * @param  string      $message
     * @param  string      $paramName  name of affected parameter
     * @param  mixed       $value      illegal value that was passed for the affected parameter
     * @param  \Exception  $cause
     * @param  int         $code
     */
    public function __construct($message, $paramName = null, $value = null, \Exception $cause = null, $code = 0)
    {
        parent::__construct($message, $cause, $code);
        $this->paramName = $paramName;
        $this->value     = $value;
    }

    /**
     * checks whether any details are known
     *
     * @return  bool
     */
    public function hasDetails()
    {
        return null !== $this->paramName;
    }

    /**
     * returns name of affected parameter
     *
     * @return  string
     * @since   2.0.0
     */
    public function getAffectedParamName()
    {
        return $this->paramName;
    }

    /**
     * returns illegal value that was passed for the affected parameter
     *
     * @return  mixed
     * @since   2.0.0
     */
    public function getIllegalParamValue()
    {
        return $this->value;
    }
}
?>