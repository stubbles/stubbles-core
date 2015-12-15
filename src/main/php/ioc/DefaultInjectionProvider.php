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
use stubbles\ioc\binding\BindingException;
use stubbles\ioc\binding\ConstantBinding;
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\ioc\binding\PropertyBinding;

use function stubbles\lang\reflect\annotationsOf;
/**
 * Default injection provider.
 *
 * Creates objects and injects all dependencies via the default injector.
 *
 * @internal
 */
class DefaultInjectionProvider implements InjectionProvider
{
    /**
     * injector to use for dependencies
     *
     * @type  \stubbles\ioc\Injector
     */
    private $injector;
    /**
     * concrete implementation to use
     *
     * @type  \ReflectionClass
     */
    private $class;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\Injector  $injector
     * @param  \ReflectionClass        $impl
     */
    public function __construct(Injector $injector, \ReflectionClass $impl)
    {
        $this->injector = $injector;
        $this->class    = $impl;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        $constructor = $this->class->getConstructor();
        if (null === $constructor || $this->class->isInternal()) {
            return $this->class->newInstance();
        }

        $params = $this->injectionValuesForMethod($constructor);
        if (count($params) === 0) {
            return $this->class->newInstance();
        }

        return $this->class->newInstanceArgs($params);
    }

    /**
     * returns a list of all injection values for given method
     *
     * @param   \ReflectionMethod  $method
     * @return  array
     * @throws  \stubbles\ioc\binding\BindingException
     */
    private function injectionValuesForMethod(\ReflectionMethod $method)
    {
        $paramValues = [];
        $defaultName = $this->methodBindingName($method);
        foreach ($method->getParameters() as $param) {
            $type  = $this->paramType($method, $param);
            $name  = $this->detectBindingName($param, $defaultName);
            $hasExplicitBinding = $this->injector->hasExplicitBinding($type, $name);
            if (!$hasExplicitBinding && $param->isDefaultValueAvailable()) {
                $paramValues[] = $param->getDefaultValue();
                continue;
            }

            if (!$this->injector->hasBinding($type, $name)) {
                $typeMsg = $this->createTypeMessage($type, $name);
                throw new BindingException(
                        'Can not inject into '
                        . $this->class->getName() . '::' . $method->getName()
                        . '(' . $this->createParamString($param, $type)
                        . '). No binding for type ' . $typeMsg
                        . ' specified. Injection stack: ' . "\n"
                        . join("\n", $this->injector->stack())
                );
            }

            $paramValues[] = $this->injector->getInstance($type, $name);
        }

        return $paramValues;
    }

    /**
     * returns default binding name for all parameters on given method
     *
     * @param   \ReflectionMethod  $method
     * @return  string
     */
    private function methodBindingName(\ReflectionMethod $method)
    {
        $annotations = annotationsOf($method);
        if ($annotations->contain('List')) {
            return $annotations->firstNamed('List')->getValue();
        }

        if ($annotations->contain('Map')) {
            return $annotations->firstNamed('Map')->getValue();
        }

        if ($annotations->contain('Named')) {
            return $annotations->firstNamed('Named')->getName();
        }

        if ($annotations->contain('Property')) {
            return $annotations->firstNamed('Property')->getValue();
        }

        return null;
    }

    /**
     * returns type of param
     *
     * @param   \ReflectionMethod     $method
     * @param   \ReflectionParameter  $param
     * @return  string
     */
    private function paramType(\ReflectionMethod $method, \ReflectionParameter $param)
    {
        $methodAnnotations = annotationsOf($method);
        $paramAnnotations  = annotationsOf($param);
        $paramClass        = $param->getClass();
        if (null !== $paramClass) {
            if ($methodAnnotations->contain('Property') || $paramAnnotations->contain('Property')) {
                return PropertyBinding::TYPE;
            }

            return $paramClass->getName();
        }

        if ($methodAnnotations->contain('List') || $paramAnnotations->contain('List')) {
            return ListBinding::TYPE;
        }

        if ($methodAnnotations->contain('Map') || $paramAnnotations->contain('Map')) {
            return MapBinding::TYPE;
        }

        if ($methodAnnotations->contain('Property') || $paramAnnotations->contain('Property')) {
            return PropertyBinding::TYPE;
        }

        return ConstantBinding::TYPE;
    }

    /**
     * detects name for binding
     *
     * @param   \ReflectionParameter  $param
     * @param   string                $default
     * @return  string|\ReflectionClass
     */
    private function detectBindingName(\ReflectionParameter $param, $default)
    {
        $annotations = annotationsOf($param);
        if ($annotations->contain('List')) {
            return $annotations->firstNamed('List')->getValue();
        }

        if ($annotations->contain('Map')) {
            return $annotations->firstNamed('Map')->getValue();
        }

        if ($annotations->contain('Named')) {
            return $annotations->firstNamed('Named')->getName();
        }

        if ($annotations->contain('Property')) {
            return $annotations->firstNamed('Property')->getValue();
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
     * @param   \ReflectionParameter  $parameter
     * @param   string                $type
     * @return  string
     */
    private function createParamString(\ReflectionParameter $parameter, $type)
    {
        $message = '';
        if (!in_array($type, [PropertyBinding::TYPE, ConstantBinding::TYPE, ListBinding::TYPE, MapBinding::TYPE])) {
            $message .= $type . ' ';
        } elseif ($parameter->isArray()) {
            $message .= 'array ';
        }

        return $message . '$' . $parameter->getName();
    }
}
