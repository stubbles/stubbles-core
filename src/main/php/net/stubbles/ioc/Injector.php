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
     * index for faster access to bindings
     *
     * @type  BindingIndex
     */
    private $bindingIndex;

    /**
     * constructor
     *
     * @param  BindingIndex   $bindingIndex
     * @param  BindingScopes  $scopes
     * @since  1.5.0
     */
    public function __construct(BindingIndex $bindingIndex = null, BindingScopes $scopes = null)
    {
        $this->scopes       = ((null === $scopes) ? (new BindingScopes()) : ($scopes));
        $this->bindingIndex = ((null === $bindingIndex) ? (new BindingIndex()) : ($bindingIndex));
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
        $this->bindingIndex->addBinding($binding);
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
        return $this->bindingIndex->hasBinding($type, $name);
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
     * returns the binding for a name and type
     *
     * @param   string  $type
     * @param   string  $name
     * @return  Binding
     */
    protected function getBinding($type, $name = null)
    {
        $binding = $this->bindingIndex->getBinding($type, $name);
        if (null !== $binding) {
            return $binding;
        }

        if (ConstantBinding::TYPE !== $type) {
            return $this->getAnnotatedBinding($type);
        }

        return null;
    }

    /**
     * returns binding denoted by annotations on type to create
     *
     * An annotated binding is when the type to create is annotated with
     * @ImplementedBy oder @ProvidedBy.
     *
     * If this is not the case it will fall back to the implicit binding.
     *
     * @param   string  $type
     * @return  Binding
     */
    protected function getAnnotatedBinding($type)
    {
        $typeClass = new ReflectionClass($type);
        if ($typeClass->isInterface() && $typeClass->hasAnnotation('ImplementedBy')) {
            return $this->bind($type)
                        ->to($typeClass->getAnnotation('ImplementedBy')
                                       ->getDefaultImplementation()
                          );
        } elseif ($typeClass->hasAnnotation('ProvidedBy')) {
            return $this->bind($type)
                        ->toProviderClass($typeClass->getAnnotation('ProvidedBy')
                                                    ->getProviderClass()
                          );
        }

        return $this->getImplicitBinding($typeClass, $type);
    }

    /**
     * returns implicit binding
     *
     * An implicit binding means that a type is requested which itself is a class
     * and not an interface. Obviously, it makes sense to say that a class is
     * always bound to itself if no other bindings where defined.
     *
     * @param   string  $type
     * @return  Binding
     */
    protected function getImplicitBinding(ReflectionClass $typeClass, $type)
    {
        if (!$typeClass->isInterface()) {
            return $this->bind($type)
                        ->to($typeClass);
        }

        return null;
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
        return $this->bindingIndex->hasBinding(ConstantBinding::TYPE, $name);
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
            if (!$method->isPublic()
              || $method->getNumberOfParameters() === 0
              || strncmp($method->getName(), '__', 2) === 0
              || !$method->hasAnnotation('Inject')) {
                continue;
            }

            try {
                $paramValues = $this->getInjectionValuesForMethod($method, $class);
            } catch (BindingException $be) {
                if (!$method->getAnnotation('Inject')->isOptional()) {
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
        $namedMethod = (($method->hasAnnotation('Named')) ? ($method->getAnnotation('Named')->getName()) : (null));
        foreach ($method->getParameters() as $param) {
            /* @type  $param  net\stubbles\lang\reflect\ReflectionParameter */
            $paramClass = $param->getClass();
            $type       = ((null !== $paramClass) ? ($paramClass->getName()) : (ConstantBinding::TYPE));
            $name       = (($param->hasAnnotation('Named')) ? ($param->getAnnotation('Named')->getName()) : ($namedMethod));
            if (!$this->hasExplicitBinding($type, $name) && $method->getAnnotation('Inject')->isOptional()) {
                // Somewhat hackish... throwing an exception here which is catched and ignored in handleInjections()
                throw new BindingException('Could not find explicit binding for optional injection of type ' . $this->createTypeMessage($type, $name) . ' to complete  ' . $this->createCalledMethodMessage($class, $method, $param, $type));
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
        } elseif ($parameter->isArray()) {
            $message .= 'array ';
        }

        return $message . '$' . $parameter->getName() . ')';
    }
}
?>