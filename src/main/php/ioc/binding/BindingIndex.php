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
use stubbles\lang\Mode;
use stubbles\lang\Properties;
use stubbles\lang\reflect\ReflectionClass;
/**
 * Stores list of all available bindings.
 *
 * @since  2.0.0
 */
class BindingIndex
{
    /**
     * list of available binding scopes
     *
     * @type  \stubbles\ioc\binding\BindingScopes
     */
    private $scopes;
    /**
     * added bindings that are in the index not yet
     *
     * @type  \stubbles\ioc\binding\Binding[]
     */
    private $bindings = [];
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
     * special binding for properties
     *
     * @type  \stubbles\ioc\binding\PropertyBinding
     */
    private $propertyBinding;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\binding\BindingScopes  $scopes
     */
    public function __construct(BindingScopes $scopes = null)
    {
        $this->scopes = ((null === $scopes) ? (new BindingScopes()) : ($scopes));
    }

    /**
     * sets the session scope
     *
     * @param   \stubbles\ioc\binding\BindingScope  $sessionScope
     * @return  \stubbles\ioc\binding\BindingIndex
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->scopes->setSessionScope($sessionScope);
        return $this;
    }

    /**
     * adds a new binding to the injector
     *
     * @param   \stubbles\ioc\binding\Binding  $binding
     * @return  \stubbles\ioc\binding\Binding
     */
    public function addBinding(Binding $binding)
    {
        $this->bindings[] = $binding;
        return $binding;
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  \stubbles\ioc\binding\ClassBinding
     */
    public function bind($interface)
    {
        return $this->addBinding(new ClassBinding($interface,
                                                  $this->scopes
                                 )
               );
    }

    /**
     * checks whether property with given name is available
     *
     * @param   string  $name
     * @return  bool
     * @since   3.4.0
     */
    public function hasProperty($name)
    {
        if (null === $this->propertyBinding) {
            return false;
        }

        return $this->propertyBinding->hasProperty($name);
    }

    /**
     * binds properties
     *
     * @param   \stubbles\lang\Properties  $properties
     * @param   \stubbles\lang\Mode        $mode
     * @return  \stubbles\lang\Properties
     * @since   3.4.0
     */
    public function bindProperties(Properties $properties, Mode $mode)
    {
        $this->propertyBinding = $this->addBinding(new PropertyBinding($properties, $mode));
        return $properties;
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
     */
    public function hasConstant($name)
    {
        return $this->hasBinding(ConstantBinding::TYPE, $name);
    }

    /**
     * bind a constant
     *
     * @param   string  $name  name of constant to bind
     * @return  \stubbles\ioc\binding\ConstantBinding
     */
    public function bindConstant($name)
    {
        return $this->addBinding(new ConstantBinding($name));
    }

    /**
     * bind to a list
     *
     * If a list with given name already exists it will return exactly this list
     * to add more values to it.
     *
     * @param   string  $name
     * @return  \stubbles\ioc\binding\ListBinding
     */
    public function bindList($name)
    {
        if ($this->hasBinding(ListBinding::TYPE, $name)) {
            return $this->getBinding(ListBinding::TYPE, $name);
        }

        return $this->addBinding(new ListBinding($name));
    }

    /**
     * bind to a map
     *
     * If a map with given name already exists it will return exactly this map
     * to add more key-value pairs to it.
     *
     * @param   string  $name
     * @return  \stubbles\ioc\binding\MapBinding
     */
    public function bindMap($name)
    {
        if ($this->hasBinding(MapBinding::TYPE, $name)) {
            return $this->getBinding(MapBinding::TYPE, $name);
        }

        return $this->addBinding(new MapBinding($name));
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
        if (PropertyBinding::TYPE === $type) {
            return $this->hasProperty($name);
        }

        return ($this->findBinding($type, $name) != null);
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() is called or an object of this type is requested.
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasExplicitBinding($type, $name = null)
    {
        if (PropertyBinding::TYPE === $type) {
            return $this->hasProperty($name);
        }

        $bindingIndex = $this->getIndex();
        if (null !== $name && isset($bindingIndex[$type . '#' . $name])) {
            return true;
        }

        if (isset($bindingIndex[$type])) {
            return true;
        }

        return false;
    }

    /**
     * checks whether list binding for given name exists
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasList($name)
    {
        return $this->hasBinding(ListBinding::TYPE, $name);
    }

    /**
     * checks whether map binding for given name exists
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasMap($name)
    {
        return $this->hasBinding(MapBinding::TYPE, $name);
    }

    /**
     * returns the binding for a name and type
     *
     * @param   string  $type
     * @param   string  $name
     * @return  \stubbles\ioc\binding\Binding
     * @throws  \stubbles\ioc\binding\BindingException
     */
    public function getBinding($type, $name = null)
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
        $bindingIndex = $this->getIndex();
        if (null !== $name && isset($bindingIndex[$type . '#' . $name])) {
            return $bindingIndex[$type . '#' . $name];
        }

        if (isset($bindingIndex[$type])) {
            return $bindingIndex[$type];
        }

        if ($this->isObjectBinding($type)) {
            return $this->getAnnotatedBinding(new ReflectionClass($type));
        }

        return null;
    }

    /**
     * returns the binding for a constant
     *
     * @param   string  $name
     * @return  \stubbles\ioc\binding\ConstantBinding
     */
    public function getConstantBinding($name = null)
    {
        return $this->getBinding(ConstantBinding::TYPE, $name);
    }

    /**
     * returns the binding for a list
     *
     * @param   string  $name
     * @return  \stubbles\ioc\binding\ListBinding
     */
    public function getListBinding($name)
    {
        return $this->getBinding(ListBinding::TYPE, $name);
    }

    /**
     * returns the binding for a map
     *
     * @param   string  $name
     * @return  \stubbles\ioc\binding\MapBinding
     */
    public function getMapBinding($name)
    {
        return $this->getBinding(MapBinding::TYPE, $name);
    }

    /**
     * checks if given type allows annotated bindings
     *
     * @param   string  $type
     * @return  bool
     */
    public function isObjectBinding($type)
    {
        if (in_array($type, [PropertyBinding::TYPE, ConstantBinding::TYPE, ListBinding::TYPE, MapBinding::TYPE])) {
            return false;
        }

        return true;
    }

    /**
     * returns the binding index
     *
     * @return  \stubbles\ioc\binding\Binding[]
     */
    private function getIndex()
    {
        if (empty($this->bindings)) {
            return $this->index;
        }

        foreach ($this->bindings as $binding) {
            $this->index[$binding->getKey()] = $binding;
        }

        $this->bindings = [];
        return $this->index;
    }

    /**
     * returns binding denoted by annotations on type to create
     *
     * An annotated binding is when the type to create is annotated with
     * @ImplementedBy oder @ProvidedBy.
     *
     * If this is not the case it will fall back to the implicit binding.
     *
     * @param   \stubbles\lang\reflect\ReflectionClass  $class
     * @return  \stubbles\ioc\binding\Binding
     */
    private function getAnnotatedBinding(ReflectionClass $class)
    {
        if ($class->isInterface() && $class->hasAnnotation('ImplementedBy')) {
            return $this->bind($class->getName())
                        ->to($class->annotation('ImplementedBy')
                                   ->getDefaultImplementation()
                          );
        } elseif ($class->hasAnnotation('ProvidedBy')) {
            return $this->bind($class->getName())
                        ->toProviderClass($class->annotation('ProvidedBy')
                                                ->getProviderClass()
                          );
        }

        return $this->getImplicitBinding($class);
    }

    /**
     * returns implicit binding
     *
     * An implicit binding means that a type is requested which itself is a class
     * and not an interface. Obviously, it makes sense to say that a class is
     * always bound to itself if no other bindings were defined.
     *
     * @param   \stubbles\lang\reflect\ReflectionClass  $class
     * @return  \stubbles\ioc\binding\Binding
     */
    private function getImplicitBinding(ReflectionClass $class)
    {
        if (!$class->isInterface()) {
            return $this->bind($class->getName())
                        ->to($class);
        }

        return null;
    }

}
