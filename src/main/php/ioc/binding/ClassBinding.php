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
use stubbles\ioc\ClosureInjectionProvider;
use stubbles\ioc\DefaultInjectionProvider;
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\Injector;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\reflect;
/**
 * Binding to bind an interface to an implementation.
 *
 * Please note that you can do a binding to a class or to an instance, or to an
 * injection provider, or to an injection provider class. These options are
 * mutually exclusive and have a predictive order:
 * 1. Instance
 * 2. Provider instance
 * 3. Provider class
 * 4. Concrete implementation class
 */
class ClassBinding implements Binding
{
    /**
     * type for this binding
     *
     * @type  string
     */
    private $type;
    /**
     * class that implements this binding
     *
     * @type  string|\ReflectionClass
     */
    private $impl;
    /**
     * Annotated with a name
     *
     * @type  string
     */
    private $name;
    /**
     * scope of the binding
     *
     * @type  \stubbles\ioc\binding\BindingScope
     */
    private $scope;
    /**
     * instance this type is bound to
     *
     * @type  object
     */
    private $instance;
    /**
     * provider to use for this binding
     *
     * @type  \stubbles\ioc\InjectionProvider
     */
    private $provider;
    /**
     * provider class to use for this binding (will be created via injector)
     *
     * @type  string
     */
    private $providerClass;
    /**
     * list of available binding scopes
     *
     * @type  \stubbles\ioc\binding\BindingScopes
     */
    private $scopes;

    /**
     * constructor
     *
     * @param  string                               $type
     * @param  \stubbles\ioc\binding\BindingScopes  $scopes
     */
    public function __construct($type, BindingScopes $scopes)
    {
        $this->type     = $type;
        $this->impl     = $type;
        $this->scopes   = $scopes;
    }

    /**
     * set the concrete implementation
     *
     * @api
     * @param   \ReflectionClass|string  $impl
     * @return  \stubbles\ioc\binding\ClassBinding
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function to($impl)
    {
        if (!is_string($impl) && !($impl instanceof \ReflectionClass)) {
            throw new IllegalArgumentException('$impl must be a string or an instance of \ReflectionClass');
        }

        $this->impl = $impl;
        return $this;
    }

    /**
     * set the concrete instance
     *
     * This cannot be used in conjuction with the 'toProvider()' or
     * 'toProviderClass()' method.
     *
     * @api
     * @param   object  $instance
     * @return  \stubbles\ioc\binding\ClassBinding
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function toInstance($instance)
    {
        if (!($instance instanceof $this->type)) {
            throw new IllegalArgumentException('Instance of ' . $this->type . ' expectected, ' . get_class($instance) . ' given.');
        }

        $this->instance = $instance;
        return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProviderClass()' method.
     *
     * @api
     * @param   \stubbles\ioc\InjectionProvider  $provider
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function toProvider(InjectionProvider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProvider()' method.
     *
     * @api
     * @param   string|\ReflectionClass  $providerClass
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function toProviderClass($providerClass)
    {
        $this->providerClass = (($providerClass instanceof \ReflectionClass) ?
                                    ($providerClass->getName()) : ($providerClass));
        return $this;
    }

    /**
     * sets a closure which can create the instance
     *
     * @api
     * @param   \Closure  $closure
     * @return  \stubbles\ioc\binding\ClassBinding
     * @since   2.1.0
     */
    public function toClosure(\Closure $closure)
    {
        $this->provider = new ClosureInjectionProvider($closure);
        return $this;
    }

    /**
     * binds the class to the singleton scope
     *
     * @api
     * @return  \stubbles\ioc\binding\ClassBinding
     * @since   1.5.0
     */
    public function asSingleton()
    {
        $this->scope = $this->scopes->singleton();
        return $this;
    }

    /**
     * binds the class to the session scope
     *
     * @api
     * @return  \stubbles\ioc\binding\ClassBinding
     * @since   1.5.0
     */
    public function inSession()
    {
        $this->scope = $this->scopes->session();
        return $this;
    }

    /**
     * set the scope
     *
     * @api
     * @param   \stubbles\ioc\binding\BindingScope  $scope
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function in(BindingScope $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Set the name of the injection
     *
     * @api
     * @param   string            $name
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns the created instance
     *
     * @param   \stubbles\ioc\Injector  $injector
     * @param   string                  $name
     * @return  mixed
     * @throws  \stubbles\ioc\binding\BindingException
     */
    public function getInstance(Injector $injector, $name)
    {
        if (null !== $this->instance) {
            return $this->instance;
        }

        if (is_string($this->impl)) {
            $this->impl = new \ReflectionClass($this->impl);
        }

        if (null === $this->scope) {
            if (reflect\annotationsOf($this->impl)->contain('Singleton')) {
                $this->scope = $this->scopes->singleton();
            }
        }

        if (null === $this->provider) {
            if (null != $this->providerClass) {
                $provider = $injector->getInstance($this->providerClass);
                if (!($provider instanceof InjectionProvider)) {
                    throw new BindingException('Configured provider class ' . $this->providerClass . ' for type ' . $this->type . ' is not an instance of stubbles\ioc\InjectionProvider.');
                }

                $this->provider = $provider;
            } else {
                $this->provider = new DefaultInjectionProvider($injector, $this->impl);
            }
        }

        if (null !== $this->scope) {
            return $this->scope->getInstance($this->impl, $this->provider);
        }

        return $this->provider->get($name);
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        if (null === $this->name) {
            return $this->type;
        }

        return $this->type . '#' . $this->name;
    }
}
