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
/**
 * Class for list bindings.
 *
 * @since  2.0.0
 */
class ListBinding extends MultiBinding
{
    /**
     * This string is used when generating the key for a list binding.
     */
    const TYPE        = '__LIST__';
    /**
     * list of bindings for the list values
     *
     * @type  array
     */
    private $bindings = [];

    /**
     * adds a value to the list
     *
     * @api
     * @param   mixed  $value
     * @return  \stubbles\ioc\binding\ListBinding
     */
    public function withValue($value)
    {
        $this->bindings[] = $this->getValueCreator($value);
        return $this;
    }

    /**
     * adds a value to the list created by an injection provider
     *
     * @api
     * @param   string|\stubbles\ioc\InjectionProvider  $provider
     * @return  \stubbles\ioc\binding\ListBinding
     */
    public function withValueFromProvider($provider)
    {
        $this->bindings[] = $this->getProviderCreator($provider);
        return $this;
    }

    /**
     * adds a value which is created by given closure
     *
     * @api
     * @param   \Closure  $closure
     * @return  \stubbles\ioc\binding\ListBinding
     * @since   2.1.0
     */
    public function withValueFromClosure(\Closure $closure)
    {
        $this->bindings[] = $closure;
        return $this;
    }

    /**
     * returns list of bindings for the list to create
     *
     * @return  array
     */
    protected function getBindings()
    {
        return $this->bindings;
    }

    /**
     * returns type key for for this binding
     *
     * @return  string
     */
    protected function getTypeKey()
    {
        return self::TYPE;
    }
}
