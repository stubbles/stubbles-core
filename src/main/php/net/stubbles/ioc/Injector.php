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
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionClass;
use net\stubbles\lang\reflect\ReflectionMethod;
use net\stubbles\lang\reflect\ReflectionParameter;
/**
 * Injector for the IoC functionality.
 *
 * Used to create the instances.
 */
class Injector extends BaseObject
{
    /**
     * list of available binding scopes
     *
     * @type  BindingScopes
     */
    protected $scopes;
    /**
     * bindings used by the injector that are not yet in the index
     *
     * @type  Binding[]
     */
    protected $bindings   = array();
    /**
     * index for faster access to bindings
     *
     * Do not access this array directly, use getIndex() instead. The binding
     * index is a requirement because the key for a binding is not necessarily
     * complete when the binding is added to the injector.
     *
     * @type  net\stubbles\ioc\Binding[]
     */
    private $bindingIndex = array();

    /**
     * constructor
     *
     * @param  BindingScopes  $scopes
     * @since  1.5.0
     */
    public function __construct(BindingScopes $scopes = null)
    {
        $this->scopes = ((null === $scopes) ? (new BindingScopes()) : ($scopes));
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   BindingScope  $sessionScope
     * @return  Binder
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->scopes->setSessionScope($sessionScope);
        return $this;
    }

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
     * creates and adds a class binding
     *
     * @param   string  $interface
     * @return  ClassBinding
     * @since   1.5.0
     */
    public function bind($interface)
    {
        return $this->addBinding(new ClassBinding($this,
                                                  $interface,
                                                  $this->scopes
                                 )
               );
    }

    /**
     * creates and adds a constanct binding
     *
     * @return  ConstantBinding
     * @since   1.5.0
     */
    public function bindConstant()
    {
        return $this->addBinding(new ConstantBinding($this));
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasBinding($type, $name = null)
    {
        return ($this->getBinding($type, $name) != null);
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() or getInstance() are called.
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasExplicitBinding($type, $name = null)
    {
        $bindingIndex = $this->getIndex();
        if (null !== $name) {
            if (isset($bindingIndex[$type . '#' . $name]) === true) {
                return true;
            }
        }

        return isset($bindingIndex[$type]);
    }

    /**
     * get an instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  object
     * @throws  BindingException
     */
    public function getInstance($type, $name = null)
    {
        $binding = $this->getBinding($type, $name);
        if (null === $binding) {
            throw new BindingException('No binding for ' . $type . ' defined');
        }

        return $binding->getInstance($type, $name);
    }

    /**
     * check whether a constant is available
     *
     * There is no need to distinguish between explicit and implicit binding for
     * constant bindings as there are only explicit constant bindings and never
     * implicit ones.
     *
     * @param   string  $name  name of constant to check for
     * @return  bool
     * @since   1.1.0
     */
    public function hasConstant($name)
    {
        return $this->hasBinding(ConstantBinding::TYPE, $name);
    }

    /**
     * returns constanct value
     *
     * @param   string  $name  name of constant value to retrieve
     * @return  scalar
     * @since   1.1.0
     */
    public function getConstant($name)
    {
        return $this->getInstance(ConstantBinding::TYPE, $name);
    }

    /**
     * returns the binding for a name and type
     *
     * @param   string  $type
     * @param   string  $name
     * @return  Binding
     */
    protected function getBinding($type, $name = null)
    {
        $bindingIndex = $this->getIndex();
        if (null !== $name) {
            if (isset($bindingIndex[$type . '#' . $name]) === true) {
                return $bindingIndex[$type . '#' . $name];
            }
        }

        if (isset($bindingIndex[$type]) === true) {
            return $bindingIndex[$type];
        }

        // prevent illegal access to reflection class for constant type
        if (ConstantBinding::TYPE === $type) {
            return null;
        }

        // check for default implementation
        $typeClass = new ReflectionClass($type);
        if ($typeClass->isInterface() === true && $typeClass->hasAnnotation('ImplementedBy') === true) {
            return $this->bind($type)
                        ->to($typeClass->getAnnotation('ImplementedBy')
                                       ->getDefaultImplementation()
                          );
        } elseif ($typeClass->hasAnnotation('ProvidedBy') === true) {
            return $this->bind($type)
                        ->toProviderClass($typeClass->getAnnotation('ProvidedBy')
                                                    ->getProviderClass()
                          );
        }

        // try implicit binding
        if ($typeClass->isInterface() === false) {
            return $this->bind($type)
                        ->to($typeClass);
        }

        return null;
    }

    /**
     * returns the binding index
     *
     * @return  net\stubbles\ioc\Binding[]
     */
    protected function getIndex()
    {
        if (empty($this->bindings) === true) {
            return $this->bindingIndex;
        }

        foreach ($this->bindings as $binding) {
            $this->bindingIndex[$binding->getKey()] = $binding;
        }

        $this->bindings = array();
        return $this->bindingIndex;
    }

    /**
     * handle injections for given instance
     *
     * @param   object               $instance
     * @param   BaseReflectionClass  $class
     * @throws  BindingException
     */
    public function handleInjections($instance, BaseReflectionClass $class = null)
    {
        if (null === $class) {
            $class = new ReflectionClass(get_class($instance));
        }

        foreach ($class->getMethods() as $method) {
            /* @type  $method  ReflectionMethod */
            if ($method->isPublic() === false
              || $method->getNumberOfParameters() === 0
              || strncmp($method->getName(), '__', 2) === 0
              || $method->hasAnnotation('Inject') === false) {
                continue;
            }

            try {
                $paramValues = $this->getInjectionValuesForMethod($method, $class);
            } catch (BindingException $be) {
                if ($method->getAnnotation('Inject')->isOptional() === false) {
                    throw $be;
                }

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
        $namedMethod = (($method->hasAnnotation('Named') === true) ? ($method->getAnnotation('Named')->getName()) : (null));
        foreach ($method->getParameters() as $param) {
            /* @type  $param  net\stubbles\lang\reflect\ReflectionParameter */
            $paramClass = $param->getClass();
            $type       = ((null !== $paramClass) ? ($paramClass->getName()) : (ConstantBinding::TYPE));
            $name       = (($param->hasAnnotation('Named') === true) ? ($param->getAnnotation('Named')->getName()) : ($namedMethod));
            if ($this->hasExplicitBinding($type, $name) === false && $method->getAnnotation('Inject')->isOptional() === true) {
                // Somewhat hackish... throwing an exception here which is catched and ignored in handleInjections()
                throw new BindingException('Could not find explicit binding for optional injection of type ' . $this->createTypeMessage($type, $name) . ' to complete  ' . $this->createCalledMethodMessage($class, $method, $param, $type));
            }

            if ($this->hasBinding($type, $name) === false) {
                $typeMsg = $this->createTypeMessage($type, $name);
                throw new BindingException('Can not inject into ' . $this->createCalledMethodMessage($class, $method, $param, $type)  . '. No binding for type ' . $typeMsg . ' specified.');
            }

            $paramValues[] = $this->getInstance($type, $name);
        }

        return $paramValues;
    }

    /**
     * creates the complete type message
     *
     * @param   string  $type  type to create message for
     * @param   string  $name  name of named parameter
     * @return  string
     */
    protected function createTypeMessage($type, $name)
    {
        return ((null !== $name) ? ($type . ' (named "' . $name . '")') : ($type));
    }

    /**
     * creates the called method message
     *
     * @param   BaseReflectionClass  $class
     * @param   ReflectionMethod     $method
     * @param   ReflectionParameter  $parameter
     * @param   string                   $type
     * @return  string
     */
    protected function createCalledMethodMessage(BaseReflectionClass $class, ReflectionMethod $method, ReflectionParameter $parameter, $type)
    {
        $message = $class->getName() . '::' . $method->getName() . '(';
        if (ConstantBinding::TYPE !== $type) {
            $message .= $type . ' ';
        } elseif ($parameter->isArray() === true) {
            $message .= 'array ';
        }

        return $message . '$' . $parameter->getName() . ')';
    }
}
?>