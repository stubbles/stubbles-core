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
use stubbles\ioc\binding\BindingScopes;
use stubbles\ioc\binding\ClassBinding;
use stubbles\ioc\binding\ConstantBinding;
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\ioc\binding\PropertyBinding;
use stubbles\ioc\binding\Session;
use stubbles\reflect\annotation\Annotations;

use function stubbles\reflect\annotationsOf;
/**
 * Injector for the IoC functionality.
 *
 * Used to create the instances.
 */
class Injector
{
    /**
     * @type  string
     */
    private $environment;
    /**
     * index for faster access to bindings
     *
     * Do not access this array directly, use getIndex() instead. The binding
     * index is a requirement because the key for a binding is not necessarily
     * complete when the binding is added to the injector.
     *
     * @type  \stubbles\ioc\binding\Binding[]
     */
    private $index    = [];
    /**
     * list of available binding scopes
     *
     * @type  \stubbles\ioc\binding\BindingScopes
     */
    private $scopes;
    /**
     * denotes how deep in the object graph the current injection takes place
     *
     * @type  string[]
     */
    private $injectionStack = [];

    /**
     * constructor
     *
     * @param  string                               $environment  optional  name of current environment
     * @param  \stubbles\ioc\binding\Binding[]      $bindings     optional
     * @param  \stubbles\ioc\binding\BindingScopes  $scopes       optional
     * @since  1.5.0
     */
    public function __construct($environment = null, array $bindings = [], BindingScopes $scopes = null)
    {
        $this->environment = $environment;
        $this->scopes      = $scopes !== null ? $scopes : new BindingScopes();
        foreach ($bindings as $binding) {
            $this->index[$binding->getKey()] = $binding;
        }
    }

    /**
     * sets the session for the session scope in case it is the built-in implementation
     *
     * Additionally, it binds the given session interface name to the session
     * instance. If no interface is given it uses the session instances class
     * name.
     *
     * @param   \stubbles\ioc\binding\Session  $session
     * @param   string                         $sessionInterface  optional
     * @return  \stubbles\ioc\Injector
     * @since   5.4.0
     */
    public function setSession(Session $session, $sessionInterface = null)
    {
        $this->scopes->setSession($session);
        $binding = $this->bind(null !== $sessionInterface ? $sessionInterface : get_class($session))
                ->toInstance($session);
        $this->index[$binding->getKey()] = $binding;
        return $this;
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
        if (PropertyBinding::TYPE === $type) {
            return $this->hasProperty($name);
        }

        return ($this->findBinding($type, $name) != null);
    }

    /**
     * checks whether property with given name is available
     *
     * @param   string  $name
     * @return  bool
     * @since   3.4.0
     */
    private function hasProperty($name)
    {
        if (!isset($this->index[PropertyBinding::TYPE])) {
            return false;
        }

        return $this->index[PropertyBinding::TYPE]->hasProperty($name);
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
        if (PropertyBinding::TYPE === $type) {
            return $this->hasProperty($name);
        }

        $bindingName = $this->bindingName($name);
        if (null !== $bindingName && isset($this->index[$type . '#' . $bindingName])) {
            return true;
        }

        if (isset($this->index[$type])) {
            return true;
        }

        return false;
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
        if (__CLASS__ === $type) {
            return $this;
        }

        array_push($this->injectionStack, $type . '#' . $name);
        $instance = $this->getBinding($type, $name)->getInstance($this, $name);
        array_pop($this->injectionStack);
        return $instance;
    }

    /**
     * returns how deep in the object graph the current injection takes place
     *
     * @return  string[]
     */
    public function stack()
    {
        return $this->injectionStack;
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
        return $this->hasBinding(ConstantBinding::TYPE, $name);
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
        return $this->getBinding(ConstantBinding::TYPE, $name)
                    ->getInstance($this, $name);
    }

    /**
     * gets a binding
     *
     * @param   string  $type
     * @param   string  $name
     * @return  \stubbles\ioc\binding\Binding
     * @throws  \stubbles\ioc\binding\BindingException
     */
    private function getBinding($type, $name = null)
    {
        $binding = $this->findBinding($type, $name);
        if (null === $binding) {
            throw new BindingException('No binding for ' . $type . ' defined');
        }

        return $binding;
    }

    /**
     * tries to find a binding
     *
     * @param   string  $type
     * @param   string  $name
     * @return  \stubbles\ioc\binding\Binding
     */
    private function findBinding($type, $name)
    {
        $bindingName = $this->bindingName($name);
        if (null !== $bindingName && isset($this->index[$type . '#' . $bindingName])) {
            return $this->index[$type . '#' . $bindingName];
        }

        if (isset($this->index[$type])) {
            return $this->index[$type];
        }

        if (!in_array($type, [PropertyBinding::TYPE, ConstantBinding::TYPE, ListBinding::TYPE, MapBinding::TYPE])) {
            $this->index[$type] = $this->getAnnotatedBinding(new \ReflectionClass($type));
            return $this->index[$type];
        }

        return null;
    }

    /**
     * parses binding name from given name
     *
     * @param   string|\ReflectionClass  $name
     * @return  string
     */
    private function bindingName($name)
    {
        if ($name instanceof \ReflectionClass) {
            return $name->getName();
        }

        return $name;
    }

    /**
     * returns binding denoted by annotations on type to create
     *
     * An annotated binding is when the type to create is annotated with
     * @ImplementedBy oder @ProvidedBy.
     *
     * If this is not the case it will fall back to the implicit binding.
     *
     * @param   \ReflectionClass  $class
     * @return  \stubbles\ioc\binding\Binding
     */
    private function getAnnotatedBinding(\ReflectionClass $class)
    {
        $annotations = annotationsOf($class);
        if ($class->isInterface() && $annotations->contain('ImplementedBy')) {
            return $this->bind($class->getName())
                    ->to($this->findImplementation($annotations, $class->getName()));
        } elseif ($annotations->contain('ProvidedBy')) {
            return $this->bind($class->getName())
                    ->toProviderClass(
                        $annotations->firstNamed('ProvidedBy')
                                ->getProviderClass()
                    );
        }

        return $this->getImplicitBinding($class);
    }

    /**
     * finds implementation to be used from list of @ImplementedBy annotations
     *
     * @param   \stubbles\reflect\annotation\Annotations  $annotations
     * @param   string                                         $type
     * @return  \ReflectionClass
     * @throws  \stubbles\ioc\binding\BindingException
     */
    private function findImplementation(Annotations $annotations, $type)
    {
        $implementation = null;
        foreach ($annotations->named('ImplementedBy') as $annotation) {
            /* @var $annotation \stubbles\reflect\annotation\Annotation */
            if (null !== $this->environment && $annotation->hasValueByName('mode') && strtoupper($annotation->getMode()) === $this->environment) {
                $implementation = $annotation->getClass();
            } elseif (!$annotation->hasValueByName('mode') && null == $implementation) {
                $implementation = $annotation->getClass();
            }
        }

        if (null === $implementation) {
            throw new BindingException('Interface ' . $type . ' annotated with @ImplementedBy, but no default found');
        }

        return $implementation;
    }

    /**
     * returns implicit binding
     *
     * An implicit binding means that a type is requested which itself is a class
     * and not an interface. Obviously, it makes sense to say that a class is
     * always bound to itself if no other bindings were defined.
     *
     * @param   \ReflectionClass  $class
     * @return  \stubbles\ioc\binding\Binding
     */
    private function getImplicitBinding(\ReflectionClass $class)
    {
        if (!$class->isInterface()) {
            return $this->bind($class->getName())->to($class);
        }

        return null;
    }

    /**
     * creates a class binding
     *
     * @param   string  $classname
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    private function bind($classname)
    {
        return new ClassBinding($classname, $this->scopes);
    }
}
