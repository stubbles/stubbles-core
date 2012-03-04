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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Stores list of all available bindings.
 *
 * @since  2.0.0
 */
class BindingIndex extends BaseObject
{
    /**
     * added bindings that are in the index not yet
     *
     * @type  Binding[]
     */
    private $bindings = array();
    /**
     * index for faster access to bindings
     *
     * Do not access this array directly, use getIndex() instead. The binding
     * index is a requirement because the key for a binding is not necessarily
     * complete when the binding is added to the injector.
     *
     * @type  Binding[]
     */
    private $index    = array();

    /**
     * adds a new binding to the injector
     *
     * @param   Binding  $binding
     * @return  Binding
     */
    public function addBinding(Binding $binding)
    {
        $this->bindings[] = $binding;
        return $binding;
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @param   string  $type
     * @param   string  $name
     * @return  bool
     */
    public function hasBinding($type, $name = null)
    {
        return ($this->getBinding($type, $name) != null);
    }

    /**
     * returns the binding for a name and type
     *
     * @param   string  $type
     * @param   string  $name
     * @return  Binding
     */
    public function getBinding($type, $name = null)
    {
        if ($name instanceof BaseReflectionClass) {
            $name = $name->getName();
        }

        $bindingIndex = $this->getIndex();
        if (null !== $name && isset($bindingIndex[$type . '#' . $name])) {
            return $bindingIndex[$type . '#' . $name];
        }

        if (isset($bindingIndex[$type])) {
            return $bindingIndex[$type];
        }

        return null;
    }

    /**
     * returns the binding index
     *
     * @return  Binding[]
     */
    private function getIndex()
    {
        if (empty($this->bindings)) {
            return $this->index;
        }

        foreach ($this->bindings as $binding) {
            $this->index[$binding->getKey()] = $binding;
        }

        $this->bindings = array();
        return $this->index;
    }
}
?>