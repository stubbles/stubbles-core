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
use net\stubbles\ioc\Injector;
use net\stubbles\ioc\Binding;
use net\stubbles\ioc\BindingException;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Base class for multi bindings.
 *
 * @since  2.0.0
 */
abstract class MultiBinding extends BaseObject implements Binding
{
    /**
     * injector instance
     *
     * @type  Injector
     */
    private $injector;
    /**
     * annotated with a name
     *
     * @type  string
     */
    private $name     = null;

    /**
     * created multi binding
     *
     * @type  array
     */
    private $array    = null;

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
     * @param   string       $name
     * @return  Binding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * creates a closure which returns the given value
     *
     * @param   mixed $value
     * @return  \Closure
     */
    protected function getValueCreator($value)
    {
        return function() use($value) { return $value; };
    }

    /**
     * creates a closure which uses the given provider to create the value
     *
     * @param   string|InjectionProvider  $provider
     * @return  \Closure
     * @throws  llegalArgumentException
     */
    protected function getProviderCreator($provider)
    {
        if (is_string($provider)) {
            return function($injector, $name, $key) use($provider)
                   {
                       $providerInstance = $injector->getInstance($provider);
                       if (!($providerInstance instanceof InjectionProvider)) {
                           throw new BindingException('Configured provider class ' . $provider . ' for ' . $name . '[' . $key . '] is not an instance of net\\stubbles\\ioc\\InjectionProvider.');
                       }

                       return $providerInstance->get();

                   };
        } elseif ($provider instanceof InjectionProvider) {
            return function() use($provider) { return $provider->get(); };
        }

        throw new IllegalArgumentException('Given provider must either be a instance of net\\stubbles\\ioc\\InjectionProvider or a class name representing such a provider instance.');
    }

    /**
     * returns the created instance
     *
     * @param   string  $name
     * @return  array
     */
    public function getInstance($name)
    {
        if (null === $this->array) {
            $this->array = $this->resolve($name);
        }

        return $this->array;
    }

    /**
     * creates the instance
     *
     * @param   string  $type
     * @return  array
     * @throws  BindingException
     */
    private function resolve($type)
    {
        $resolved = array();
        foreach ($this->getBindings() as $key => $bindingValue) {
            $value = $bindingValue($this->injector, $this->name, $key);
            if ($this->isTypeMismatch($type, $value)) {
                throw new BindingException('Value for ' . ((is_int($key)) ? ('list') : ('map')) . ' named ' . $this->name . ' at position ' . $key . ' is not of type ' . $type->getName());
            }

            $resolved[$key] = $value;
        }

        return $resolved;
    }

    /**
     * checks if given type and type of value are a mismatch
     *
     * A type mismatch is defined as follows: $value is an object and it's
     * an instance of the class defined with $type. In any other case there's no
     * type mismatch
     *
     * @param   string|BaseReflectionClass  $type
     * @param   mixed                       $value
     * @return  bool
     */
    private function isTypeMismatch($type, $value)
    {
        if (!($type instanceof BaseReflectionClass)) {
            return false;
        }

        if (!is_object($value)) {
            return true;
        }

        return !$type->isInstance($value);
    }

    /**
     * returns list of bindings for the array to create
     *
     * @return  array
     */
    protected abstract function getBindings();

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        return $this->getTypeKey() . '#' . $this->name;
    }

    /**
     * returns type key for for this binding
     *
     * @return  string
     */
    protected abstract function getTypeKey();
}
?>