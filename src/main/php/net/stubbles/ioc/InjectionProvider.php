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
use net\stubbles\lang\Object;
/**
 * Interface for providers that create objects that are required by the
 * Inversion of Control features of Stubbles.
 */
interface InjectionProvider extends Object
{
    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null);
}
?>