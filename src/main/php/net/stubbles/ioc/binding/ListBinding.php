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
    private $bindings = array();

    /**
     * adds a value to the list
     *
     * @api
     * @param   mixed  $value
     * @return  ListBinding
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
     * @param   string|InjectionProvider  $provider
     * @return  ListBinding
     */
    public function withValueFromProvider($provider)
    {
        $this->bindings[] = $this->getProviderCreator($provider);
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
?>