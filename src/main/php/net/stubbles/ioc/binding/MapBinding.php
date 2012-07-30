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
 * Class for map bindings.
 *
 * @since  2.0.0
 */
class MapBinding extends MultiBinding
{
    /**
     * This string is used when generating the key for a map binding.
     */
    const TYPE        = '__MAP__';
    /**
     * list of bindings for the map values
     *
     * @type  array
     */
    private $bindings = array();

    /**
     * adds an entry to the list
     *
     * @api
     * @param   string  $key
     * @param   mixed   $value
     * @return  MapBinding
     */
    public function withEntry($key, $value)
    {
        $this->bindings[$key] = $this->getValueCreator($value);
        return $this;
    }

    /**
     * adds an entry to the map created by an injection provider
     *
     * @api
     * @param   string                    $key
     * @param   string|InjectionProvider  $provider
     * @return  MapBinding
     */
    public function withEntryFromProvider($key, $provider)
    {
        $this->bindings[$key] = $this->getProviderCreator($provider);
        return $this;
    }

    /**
     * adds an entry which is created by given closure
     *
     * @api
     * @param   \Closure  $closure
     * @return  MapBinding
     * @since   2.1.0
     */
    public function withEntryFromClosure($key, \Closure $closure)
    {
        $this->bindings[$key] = $closure;
        return $this;
    }

    /**
     * returns list of bindings for the map to create
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