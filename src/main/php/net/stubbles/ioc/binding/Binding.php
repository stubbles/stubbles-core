<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\binding;
use net\stubbles\lang\Object;
/**
 * Binding to bind an interface to an implementation
 */
interface Binding extends Object
{
    /**
     * set the name of the injection
     *
     * @param   string       $name
     * @return  Binding
     */
    public function named($name);

    /**
     * returns the created instance
     *
     * @param   string  $name
     * @return  mixed
     */
    public function getInstance($name);

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey();
}
?>