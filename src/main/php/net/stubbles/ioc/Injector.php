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
use net\stubbles\ioc\binding\BindingException;
use net\stubbles\ioc\binding\BindingIndex;
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionClass;
use net\stubbles\lang\reflect\ReflectionMethod;
use net\stubbles\lang\reflect\ReflectionObject;
use net\stubbles\lang\reflect\ReflectionParameter;
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
     * @type  BindingIndex
     */
    private $bindingIndex;

    /**
     * constructor
     *
     * @param  BindingIndex   $bindingIndex
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
     * @param   string|BaseReflectionClass  $name
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

    /**
     * handle injections for given instance
     *
     * @param   object               $instance
     * @param   BaseReflectionClass  $class
     */
    public function handleInjections($instance, BaseReflectionClass $class = null)
    {
        if (null === $class) {
            $class = new ReflectionObject($instance);
        }

        foreach ($class->getMethods() as $method) {
            /* @type  $method  ReflectionMethod */
            if (!$method->isPublic()
              || $method->getNumberOfParameters() === 0
              || strncmp($method->getName(), '__', 2) === 0
              || !$method->hasAnnotation('Inject')) {
                continue;
            }

            $paramValues = $this->getInjectionValuesForMethod($method, $class);
            if (false === $paramValues) {
                continue;
            }

            $method->invokeArgs($instance, $paramValues);
        }
    }

    /**
     * returns a list of all injection values for given method
     *
     * @param   ReflectionMethod     $method
     * @param   BaseReflectionClass  $class
     * @return  array
     * @throws  BindingException
     */
    public function getInjectionValuesForMethod(ReflectionMethod $method, BaseReflectionClass $class)
    {
        $paramValues = array();
        $defaultName = $this->getMethodBindingName($method);
        foreach ($method->getParameters() as $param) {
            $type  = $this->getParamType($method, $param);
            $name  = $this->detectBindingName($param, $defaultName);
            if (!$this->hasExplicitBinding($type, $name) && $method->getAnnotation('Inject')->isOptional()) {
                return false;
            }

            if (!$this->hasBinding($type, $name)) {
                $typeMsg = $this->createTypeMessage($type, $name);
                throw new BindingException('Can not inject into ' . $this->createCalledMethodMessage($class, $method, $param, $type)  . '. No binding for type ' . $typeMsg . ' specified.');
            }

            $paramValues[] = $this->getInstance($type, $name);
        }

        return $paramValues;
    }

    /**
     * returns default binding name for all parameters on given method
     *
     * @param   ReflectionMethod  $method
     * @return  string
     */
    private function getMethodBindingName(ReflectionMethod $method)
    {
        if ($method->hasAnnotation('List')) {
            return $method->getAnnotation('List')->getValue();
        }

        if ($method->hasAnnotation('Map')) {
            return $method->getAnnotation('Map')->getValue();
        }

        if ($method->hasAnnotation('Named')) {
            return $method->getAnnotation('Named')->getName();
        }

        return null;
    }

    /**
     * returns type of param
     *
     * @param   ReflectionMethod     $method
     * @param   ReflectionParameter  $param
     * @return  string
     */
    private function getParamType(ReflectionMethod $method, ReflectionParameter $param)
    {
        $paramClass = $param->getClass();
        if (null !== $paramClass) {
            return $paramClass->getName();
        }

        if ($method->hasAnnotation('List') || $param->hasAnnotation('List')) {
            return BindingIndex::getListType();
        }

        if ($method->hasAnnotation('Map') || $param->hasAnnotation('Map')) {
            return BindingIndex::getMapType();
        }

        return BindingIndex::getConstantType();
    }

    /**
     * detects name for binding
     *
     * @param   ReflectionParameter  $param
     * @param   string               $default
     * @return  string|ReflectionClass
     */
    private function detectBindingName(ReflectionParameter $param, $default)
    {
        if ($param->hasAnnotation('List')) {
            return $param->getAnnotation('List')->getValue();
        }

        if ($param->hasAnnotation('Map')) {
            return $param->getAnnotation('Map')->getValue();
        }

        if ($param->hasAnnotation('Named')) {
            return $param->getAnnotation('Named')->getName();
        }

        return $default;
    }

    /**
     * creates the complete type message
     *
     * @param   string  $type  type to create message for
     * @param   string  $name  name of named parameter
     * @return  string
     */
    private function createTypeMessage($type, $name)
    {
        return ((null !== $name) ? ($type . ' (named "' . $name . '")') : ($type));
    }

    /**
     * creates the called method message
     *
     * @param   BaseReflectionClass  $class
     * @param   ReflectionMethod     $method
     * @param   ReflectionParameter  $parameter
     * @param   string               $type
     * @return  string
     */
    private function createCalledMethodMessage(BaseReflectionClass $class, ReflectionMethod $method, ReflectionParameter $parameter, $type)
    {
        $message = $class->getName() . '::' . $method->getName() . '(';
        if ($this->bindingIndex->isObjectBinding($type)) {
            $message .= $type . ' ';
        } elseif ($parameter->isArray()) {
            $message .= 'array ';
        }

        return $message . '$' . $parameter->getName() . ')';
    }
}
?>