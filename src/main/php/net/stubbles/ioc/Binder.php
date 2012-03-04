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
 * Binder for the IoC functionality.
 */
class Binder extends BaseObject
{
    /**
     * Injector used by this binder
     *
     * @type  Injector
     */
    protected $injector;

    /**
     * Create a new binder
     *
     * @param  Injector  $injector
     */
    public function __construct(Injector $injector = null)
    {
        $this->injector = ((null === $injector) ? (new Injector()) : ($injector));
    }

    /**
     * sets the session scope
     *
     * @param   BindingScope  $sessionScope
     * @return  Binder
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->injector->setSessionScope($sessionScope);
        return $this;
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  ClassBinding
     */
    public function bind($interface)
    {
        return $this->injector->bind($interface);
    }

    /**
     * Bind a new constant
     *
     * @return  ConstantBinding
     */
    public function bindConstant()
    {
        return $this->injector->bindConstant();
    }

    /**
     * bind to a list
     *
     * If a list with given name already exists it will return exactly this list
     * to add more values to it.
     *
     * @param   string  $name
     * @return  net\stubbles\ioc\binding\ListBinding
     * @since   2.0.0
     */
    public function bindList($name)
    {
        return $this->injector->bindList($name);
    }

    /**
     * bind to a map
     *
     * If a map with given name already exists it will return exactly this map
     * to add more key-value pairs to it.
     *
     * @param   string  $name
     * @return  net\stubbles\ioc\binding\MapBinding
     * @since   2.0.0
     */
    public function bindMap($name)
    {
        return $this->injector->bindMap($name);
    }

    /**
     * Get an injector for this binder
     *
     * @return  Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }
}
?>