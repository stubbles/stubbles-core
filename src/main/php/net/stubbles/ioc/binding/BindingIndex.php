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
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Stores list of all available bindings.
 *
 * @since  2.0.0
 */
class BindingIndex extends BaseObject
{
    /**
     * list of available binding scopes
     *
     * @type  BindingScopes
     */
    private $scopes;
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
     * constructor
     *
     * @param  BindingScopes  $scopes
     */
    public function __construct(BindingScopes $scopes = null)
    {
        $this->scopes = ((null === $scopes) ? (new BindingScopes()) : ($scopes));
    }

    /**
     * returns key for constant bindings
     *
     * @return  string
     */
    public static function getConstantType()
    {
        return ConstantBinding::TYPE;
    }

    /**
     * returns key for constant bindings
     *
     * @return  string
     */
    public static function getListType()
    {
        return ListBinding::TYPE;
    }

    /**
     * returns key for constant bindings
     *
     * @return  string
     */
    public static function getMapType()
    {
        return MapBinding::TYPE;
    }

    /**
     * sets the session scope
     *
     * @param   BindingScope  $sessionScope
     * @return  BindingIndex
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
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  ClassBinding
     */
    public function bind($interface)
    {
        return $this->addBinding(new ClassBinding($interface,
                                                  $this->scopes
                                 )
               );
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
     * @return  ConstantBinding
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
     * @return  ListBinding
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
     * @return  MapBinding
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
     * returns the binding for a name and type
     *
     * @param   string  $type
     * @param   string  $name
     * @return  Binding
     * @throws  BindingException
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
     * @return  Binding
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
     * @return  Binding
     */
    public function getConstantBinding($name = null)
    {
        return $this->getBinding(ConstantBinding::TYPE, $name);
    }

    /**
     * checks if given type allows annotated bindings
     *
     * @param   string  $type
     * @return  bool
     */
    public function isObjectBinding($type)
    {
        if (in_array($type, array(ConstantBinding::TYPE, ListBinding::TYPE, MapBinding::TYPE))) {
            return false;
        }

        return true;
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

    /**
     * returns binding denoted by annotations on type to create
     *
     * An annotated binding is when the type to create is annotated with
     * @ImplementedBy oder @ProvidedBy.
     *
     * If this is not the case it will fall back to the implicit binding.
     *
     * @param   ReflectionClass  $class
     * @return  Binding
     */
    private function getAnnotatedBinding(ReflectionClass $class)
    {
        if ($class->isInterface() && $class->hasAnnotation('ImplementedBy')) {
            return $this->bind($class->getName())
                        ->to($class->getAnnotation('ImplementedBy')
                                   ->getDefaultImplementation()
                          );
        } elseif ($class->hasAnnotation('ProvidedBy')) {
            return $this->bind($class->getName())
                        ->toProviderClass($class->getAnnotation('ProvidedBy')
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
     * @param   ReflectionClass  $class
     * @return  Binding
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
?>