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
use net\stubbles\ioc\binding\Binding;
use net\stubbles\ioc\binding\BindingIndex;
use net\stubbles\ioc\binding\BindingScope;
use net\stubbles\lang\BaseObject;
/**
 * Binder for the IoC functionality.
 *
 * @api
 */
class Binder extends BaseObject
{
    /**
     * index for faster access to bindings
     *
     * @type  BindingIndex
     */
    private $bindingIndex;

    /**
     * constructor
     *
     * @param  BindingIndex  $index
     */
    public function __construct(BindingIndex $index = null)
    {
        $this->bindingIndex = ((null === $index) ? (new BindingIndex()) : ($index));
    }

    /**
     * sets the session scope
     *
     * @param   BindingScope  $sessionScope
     * @return  Binder
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->bindingIndex->setSessionScope($sessionScope);
        return $this;
    }

    /**
     * adds a new binding to the injector
     *
     * @param   Binding  $binding
     * @return  Binding
     */
    public function addBinding(Binding $binding)
    {
        return $this->bindingIndex->addBinding($binding);
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  net\stubbles\ioc\binding\ClassBinding
     */
    public function bind($interface)
    {
        return $this->bindingIndex->bind($interface);
    }

    /**
     * bind a constant
     *
     * @param   string  $name  name of constant to bind
     * @return  net\stubbles\ioc\binding\ConstantBinding
     */
    public function bindConstant($name)
    {
        return $this->bindingIndex->bindConstant($name);
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
        return $this->bindingIndex->bindList($name);
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
        return $this->bindingIndex->bindMap($name);
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @param   string  $type
     * @param   string  $name
     * @return  bool
     * @since   2.0.0
     */
    public function hasBinding($type, $name = null)
    {
        return $this->bindingIndex->hasBinding($type, $name);
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() or getInstance() are called.
     *
     * @param   string  $type
     * @param   string  $name
     * @return  bool
     * @since   2.0.0
     */
    public function hasExplicitBinding($type, $name = null)
    {
        return $this->bindingIndex->hasExplicitBinding($type, $name);
    }

    /**
     * check whether a constant is available
     *
     * There is no need to distinguish between explicit and implicit binding for
     * constant bindings as there are only explicit constant bindings and never
     * implicit ones.
     *
     * @param   string  $name  name of constant to check for
     * @return  bool
     * @since   2.0.0
     */
    public function hasConstant($name)
    {
        return $this->bindingIndex->hasConstant($name);
    }

    /**
     * Get an injector for this binder
     *
     * @return  Injector
     */
    public function getInjector()
    {
        $injector = new Injector($this->bindingIndex);
        $this->bindingIndex->bind($injector->getClassName())
                           ->toInstance($injector);
        return $injector;
    }
}
?>