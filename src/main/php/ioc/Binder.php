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
use stubbles\ioc\binding\BindingScope;
use stubbles\ioc\binding\BindingScopes;
use stubbles\ioc\binding\ClassBinding;
use stubbles\ioc\binding\ConstantBinding;
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\ioc\binding\PropertyBinding;
use stubbles\ioc\binding\Session;
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
     * switch whether setter injection is enabled
     *
     * @type  bool
     * @since  5.1.0
     * @deprecated  since 5.1.0, don't use setter injection, will be removed with 6.0.0
     */
    private static $setterInjectionEnabled = false;

    /**
     * enable setter injection
     *
     * @since  5.1.0
     * @deprecated  since 5.1.0, don't use setter injection, will be removed with 6.0.0
     */
    public static function enableSetterInjection()
    {
        self::$setterInjectionEnabled = true;
    }

    /**
     * disable setter injection
     *
     * @since  5.1.0
     * @deprecated  since 5.1.0, will be removed with 6.0.0
     */
    public static function disableSetterInjection()
    {
        self::$setterInjectionEnabled = false;
    }
    /**
     * checks whether setter injection is enabled
     *
     * @return  bool
     * @since  5.1.0
     * @deprecated  since 5.1.0, don't use setter injection, will be removed with 6.0.0
     */
    public static function isSetterInjectionEnabled()
    {
        return self::$setterInjectionEnabled;
    }

    /**
     * list of available binding scopes
     *
     * @type  \stubbles\ioc\binding\BindingScopes
     */
    private $scopes;
    /**
     * added bindings that are in the index not yet
     *
     * @type  \stubbles\ioc\binding\Binding[]
     */
    private $bindings = [];
    /**
     * map of list bindings
     *
     * @type  \stubbles\ioc\binding\ListBinding[]
     */
    private $listBindings = [];
    /**
     * map of map bindings
     *
     * @type  \stubbles\ioc\binding\MapBinding[]
     */
    private $mapBindings  = [];

    /**
     * constructor
     *
     * @param  \stubbles\ioc\binding\BindingScopes  $scopes  optional
     */
    public function __construct(BindingScopes $scopes = null)
    {
        $this->scopes = ((null === $scopes) ? (new BindingScopes()) : ($scopes));
    }

    /**
     * sets the session scope
     *
     * @param   \stubbles\ioc\binding\BindingScope  $sessionScope
     * @return  \stubbles\ioc\Binder
     * @deprecated  since 5.4.0, use built-in session scope with session interface instead, will be removed with 6.0.0
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->scopes->setSessionScope($sessionScope);
        return $this;
    }

    /**
     * sets the session for the session scope in case it is the built-in implementation
     *
     * @param   \stubbles\ioc\binding\Session  $session
     * @return  \stubbles\ioc\Injector
     * @since   5.4.0
     * @deprecated  since 5.4.0, only for compatibility of built-in scope and setting in binding module, will be removed with 6.0.0
     */
    public function setSession(Session $session)
    {
        $this->scopes->setSession($session);
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
         $this->bindings[] = $binding;
         return $binding;
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function bind($interface)
    {
        return $this->addBinding(new ClassBinding($interface, $this->scopes));
    }

    /**
     * binds properties from given properties file
     *
     * @param   string               $propertiesFile  file where properties are stored
     * @param   \stubbles\lang\Mode  $mode
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
        $this->addBinding(new PropertyBinding($properties, $mode));
        $this->bind('stubbles\lang\Properties')
             ->named('config.ini')
             ->toInstance($properties);
        return $properties;
    }

    /**
     * bind a constant
     *
     * @param   string  $name  name of constant to bind
     * @return  \stubbles\ioc\binding\ConstantBinding
     */
    public function bindConstant($name)
    {
        return $this->addBinding(new ConstantBinding($name));
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
        if (!isset($this->listBindings[$name])) {
            $this->listBindings[$name] = $this->addBinding(new ListBinding($name));
        }

        return $this->listBindings[$name];
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
        if (!isset($this->mapBindings[$name])) {
            $this->mapBindings[$name] = $this->addBinding(new MapBinding($name));
        }

        return $this->mapBindings[$name];
    }

    /**
     * Get an injector for this binder
     *
     * @return  \stubbles\ioc\Injector
     */
    public function getInjector()
    {
        return new Injector($this->bindings, $this->scopes);
    }
}
