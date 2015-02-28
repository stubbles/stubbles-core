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
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\Injector;
/**
 * Binding to bind a property to a constant value.
 */
class ConstantBinding implements Binding
{
    /**
     * This string is used when generating the key for a constant binding.
     */
    const TYPE             = '__CONSTANT__';
    /**
     * annotated with a name
     *
     * @type  string
     */
    private $name          = null;
    /**
     * value to provide
     *
     * @type  mixed
     */
    private $value;
    /**
     * provider to use for this binding
     *
     * @type  \stubbles\ioc\InjectionProvider
     */
    private $provider      = null;
    /**
     * provider class to use for this binding (will be created via injector)
     *
     * @type  string
     */
    private $providerClass = null;

    /**
     * constructor
     *
     * @param  string  $name  name of the list or map
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * set the constant value
     *
     * @api
     * @param   mixed  $value
     * @return  ConstantBinding
     */
    public function to($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProviderClass()' method.
     *
     * @api
     * @param   \stubbles\ioc\InjectionProvider  $provider
     * @return  \stubbles\ioc\binding\ConstantBinding
     * @since   1.6.0
     */
    public function toProvider(InjectionProvider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProvider()' method.
     *
     * @api
     * @param   string|\ReflectionClass  $providerClass
     * @return  \stubbles\ioc\binding\ConstantBinding
     * @since   1.6.0
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
     * @return  \stubbles\ioc\binding\ConstantBinding
     * @since   2.1.0
     */
    public function toClosure(\Closure $closure)
    {
        $this->provider = new ClosureInjectionProvider($closure);
        return $this;
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        return self::TYPE . '#' . $this->name;
    }

    /**
     * returns the created instance
     *
     * @param   \stubbles\ioc\Injector  $injector
     * @param   string                  $name
     * @return  mixed
     */
    public function getInstance(Injector $injector, $name)
    {
        if (null !== $this->provider) {
            return $this->provider->get($name);
        }

        if (null != $this->providerClass) {
            $provider = $injector->getInstance($this->providerClass);
            if (!($provider instanceof InjectionProvider)) {
                 throw new BindingException('Configured provider class ' . $this->providerClass . ' for constant ' . $this->name . ' is not an instance of stubbles\ioc\InjectionProvider.');
            }

            $this->provider = $provider;
            return $this->provider->get($name);
        }

        return $this->value;
    }
}
