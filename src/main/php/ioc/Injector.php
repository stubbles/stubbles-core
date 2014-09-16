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
use stubbles\ioc\binding\BindingIndex;
use stubbles\lang\reflect\BaseReflectionClass;
/**
 * Injector for the IoC functionality.
 *
 * Used to create the instances.
 */
class Injector
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
     * @param  \stubbles\ioc\binding\BindingIndex   $bindingIndex
     * @since  1.5.0
     */
    public function __construct(BindingIndex $bindingIndex)
    {
        $this->bindingIndex = $bindingIndex;
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @api
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasBinding($type, $name = null)
    {
        return $this->bindingIndex->hasBinding($type, $this->getBindingName($name));
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() or getInstance() are called.
     *
     * @api
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasExplicitBinding($type, $name = null)
    {
        return $this->bindingIndex->hasExplicitBinding($type, $this->getBindingName($name));
    }

    /**
     * get an instance
     *
     * @api
     * @param   string  $type
     * @param   string  $name
     * @return  object
     */
    public function getInstance($type, $name = null)
    {
        return $this->bindingIndex->getBinding($type, $this->getBindingName($name))
                                  ->getInstance($this, $name);
    }

    /**
     * parses binding name from given name
     *
     * @param   string|\stubbles\lang\reflect\BaseReflectionClass  $name
     * @return  string
     */
    private function getBindingName($name)
    {
        if ($name instanceof BaseReflectionClass) {
            return $name->getName();
        }

        return $name;
    }

    /**
     * check whether a constant is available
     *
     * @api
     * @param   string  $name  name of constant to check for
     * @return  bool
     * @since   1.1.0
     */
    public function hasConstant($name)
    {
        return $this->bindingIndex->hasConstant($name);
    }

    /**
     * returns constanct value
     *
     * @api
     * @param   string  $name  name of constant value to retrieve
     * @return  scalar
     * @since   1.1.0
     */
    public function getConstant($name)
    {
        return $this->bindingIndex->getConstantBinding($name)
                                  ->getInstance($this, $name);
    }

    /**
     * checks whether list binding for given name exists
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasList($name)
    {
        return $this->bindingIndex->hasList($name);
    }

    /**
     * returns list for given name
     *
     * @param   string  $name
     * @return  array
     */
    public function getList($name)
    {
        return $this->bindingIndex->getListBinding($name)
                                  ->getInstance($this, $name);
    }

    /**
     * checks whether map binding for given name exists
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasMap($name)
    {
        return $this->bindingIndex->hasMap($name);
    }

    /**
     * returns map for given name
     *
     * @param   string  $name
     * @return  array
     */
    public function getMap($name)
    {
        return $this->bindingIndex->getMapBinding($name)
                                  ->getInstance($this, $name);
    }
}
