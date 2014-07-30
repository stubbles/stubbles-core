<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\ioc\binding\Binding;
use stubbles\ioc\binding\BindingIndex;
use stubbles\ioc\binding\BindingScope;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
/**
 * Binder for the IoC functionality.
 *
 * @api
 */
class Binder
{
    /**
     * index for faster access to bindings
     *
     * @type  \stubbles\ioc\binding\BindingIndex
     */
    private $bindingIndex;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\binding\BindingIndex  $index
     */
    public function __construct(BindingIndex $index = null)
    {
        $this->bindingIndex = ((null === $index) ? (new BindingIndex()) : ($index));
    }

    /**
     * sets the session scope
     *
     * @param   \stubbles\ioc\binding\BindingScope  $sessionScope
     * @return  \stubbles\ioc\Binder
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->bindingIndex->setSessionScope($sessionScope);
        return $this;
    }

    /**
     * adds a new binding to the injector
     *
     * @param   \stubbles\ioc\binding\Binding  $binding
     * @return  \stubbles\ioc\binding\Binding
     */
    public function addBinding(Binding $binding)
    {
        return $this->bindingIndex->addBinding($binding);
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function bind($interface)
    {
        return $this->bindingIndex->bind($interface);
    }

    /**
     * binds properties from given properties file
     *
     * @param   string                $propertiesFile  file where properties are stored
     * @param   \stubbles\lang\Mode   $mode
     * @return  \stubbles\lang\Properties
     * @since   4.0.0
     */
    public function bindPropertiesFromFile($propertiesFile, Mode $mode)
    {
        return $this->bindProperties(Properties::fromFile($propertiesFile), $mode);
    }

    /**
     * binds properties
     *
     * @param   \stubbles\lang\Properties  $properties
     * @param   \stubbles\lang\Mode        $mode
     * @return  \stubbles\lang\Properties
     * @since   3.4.0
     */
    public function bindProperties(Properties $properties, Mode $mode)
    {
        return $this->bindingIndex->bindProperties($properties, $mode);
    }

    /**
     * bind a constant
     *
     * @param   string  $name  name of constant to bind
     * @return  \stubbles\ioc\binding\ConstantBinding
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
     * @return  \stubbles\ioc\binding\ListBinding
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
     * @return  \stubbles\ioc\binding\MapBinding
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
     * checks whether property with given name is available
     *
     * @param   string  $name
     * @return  bool
     * @since   3.4.0
     */
    public function hasProperty($name)
    {
        return $this->bindingIndex->hasProperty($name);
    }

    /**
     * Get an injector for this binder
     *
     * @return  \stubbles\ioc\Injector
     */
    public function getInjector()
    {
        $injector = new Injector($this->bindingIndex);
        $this->bindingIndex->bind(get_class($injector))
                           ->toInstance($injector);
        return $injector;
    }
}
