<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use net\stubbles\lang\BaseObject;
/**
 * Simple injection provider for a single predefined value.
 */
class ValueInjectionProvider extends BaseObject implements InjectionProvider
{
    /**
     * value to provide
     *
     * @type  mixed
     */
    protected $value;

    /**
     * constructor
     *
     * @param  mixed  $value  value to provide
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  name of the value
     * @return  mixed
     */
    public function get($name = null)
    {
        return $this->value;
    }
}
?>