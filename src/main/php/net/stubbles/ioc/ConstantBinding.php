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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Binding to bind a property to a constant value.
 */
class ConstantBinding extends BaseObject implements Binding
{
    /**
     * This string is used when generating the key for a constant binding.
     */
    const TYPE               = '__CONSTANT__';
    /**
     * injector used by this binding
     *
     * @type  Injector
     */
    protected $injector      = null;
    /**
     * annotated with a name
     *
     * @type  string
     */
    protected $name          = null;
    /**
     * value to provide
     *
     * @type  mixed
     */
    protected $value;
    /**
     * provider to use for this binding
     *
     * @type  InjectionProvider
     */
    protected $provider      = null;
    /**
     * provider class to use for this binding (will be created via injector)
     *
     * @type  string
     */
    protected $providerClass = null;

    /**
     * constructor
     *
     * @param  Injector  $injector
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * set the name of the injection
     *
     * @param   string               $name
     * @return  ConstantBinding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set the constant value
     *
     * @param   mixed                $value
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
     * @param   InjectionProvider  $provider
     * @return  ConstantBinding
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
     * @param   string|BaseReflectionClass  $providerClass
     * @return  ConstantBinding
     * @since   1.6.0
     */
    public function toProviderClass($providerClass)
    {
        $this->providerClass = (($providerClass instanceof BaseReflectionClass) ?
                                    ($providerClass->getName()) : ($providerClass));
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
     * returns the value to provide
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  BindingException
     */
    public function getInstance($type, $name)
    {
        if (null !== $this->provider) {
            return $this->provider->get($name);
        }

        if (null != $this->providerClass) {
            $provider = $this->injector->getInstance($this->providerClass);
            if (!($provider instanceof InjectionProvider)) {
                 throw new BindingException('Configured provider class ' . $this->providerClass . ' for constant ' . $this->name . ' is not an instance of net\stubbles\ioc\InjectionProvider.');
            }

            $this->provider = $provider;
            return $this->provider->get($name);
        }

        return $this->value;
    }
}
?>