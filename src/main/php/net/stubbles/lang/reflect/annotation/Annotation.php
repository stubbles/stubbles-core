<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\MethodNotSupportedException;
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Interface for an annotation.
 */
class Annotation extends BaseObject
{
    /**
     * annotation is applicable for classes
     */
    const TARGET_CLASS    = 1;
    /**
     * annotation is applicable for properties
     */
    const TARGET_PROPERTY = 2;
    /**
     * annotation is applicable for methods
     */
    const TARGET_METHOD   = 4;
    /**
     * annotation is applicable for functions
     */
    const TARGET_FUNCTION = 8;
    /**
     * annotation is applicable for parameters
     */
    const TARGET_PARAM    = 16;
    /**
     * annotation is applicable for classes, properties, methods and functions
     */
    const TARGET_ALL      = 31;
    /**
     * name of annotation
     *
     * @type  string
     */
    protected $name;
    /**
     * properties of annotation
     *
     * @type  array
     */
    protected $properties = array();

    /**
     * constructor
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name under which the annotation is stored.
     *
     * @return  string
     */
    public function getAnnotationName()
    {
        return $this->name;
    }

    /**
     * sets a single value
     *
     * @param  mixed  $value
     */
    public function setValue($value)
    {
        $this->properties['__value'] = $value;
    }

    /**
     * checks whether a value with given name exists
     *
     * Returns null if a value with given name does not exist or is not set.
     *
     * @param   string  $name
     * @return  bool
     * @since   1.7.0
     */
    public function hasValueByName($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * returns a value by its name
     *
     * Returns null if a value with given name does not exist or is not set.
     *
     * @param   string  $name
     * @return  mixed
     * @since   1.7.0
     */
    public function getValueByName($name)
    {
        if (isset($this->properties[$name]) === true) {
            return $this->properties[$name];
        }

        return null;
    }

    /**
     * sets value for given property
     *
     * @param  string  $name
     * @param  mixed   $value
     */
    public function  __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * responds to a method call of an undefined method
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     * @throws  MethodNotSupportedException
     */
    public function  __call($name, $arguments)
    {
        if (isset($this->properties[$name]) === true) {
            return $this->properties[$name];
        }

        if (substr($name, 0, 3) === 'get') {
            return $this->getProperty(strtolower(substr($name, 3, 1)) . substr($name, 4),
                                      $this->extractDefaultValue($arguments)
                    );
        }

        if (substr($name, 0, 2) === 'is') {
            return $this->getBooleanProperty(strtolower(substr($name, 2, 1)) . substr($name, 3));
        }

        if (substr($name, 0, 3) === 'has') {
            return $this->hasProperty(strtolower(substr($name, 3, 1)) . substr($name, 4));
        }

        throw new MethodNotSupportedException('The method ' . $name . ' does not exit.');
    }

    /**
     * returns first value in array or null if it does not exist
     *
     * @param   array  $arguments
     * @return  miced
     */
    protected function extractDefaultValue(array $arguments)
    {
        if (count($arguments) === 0) {
            return null;
        }

        return array_shift($arguments);
    }

    /**
     * returns property which is retrieved via get$PROPERTYNAME()
     *
     * @param   string  $propertyName
     * @param   mixed   $defaultValue
     * @return  mixed
     */
    protected function getProperty($propertyName, $defaultValue)
    {
        if (count($this->properties) === 1 && isset($this->properties['__value']) === true) {
            return $this->properties['__value'];
        }

        if (isset($this->properties[$propertyName]) === true) {
            return $this->properties[$propertyName];
        }

        return $defaultValue;
    }

    /**
     * returns boolean property which is retrieved via is$PROPERTYNAME()
     *
     * @param   string  $propertyName
     * @return  bool
     */
    protected function getBooleanProperty($propertyName)
    {
        if (count($this->properties) === 1 && isset($this->properties['__value']) === true) {
            return $this->properties['__value'];
        }

        if (isset($this->properties[$propertyName]) === true) {
            return $this->properties[$propertyName];
        }

        return false;
    }

    /**
     * checks if property which is checked via has$PROPERTYNAME() is set
     *
     * @param   string  $propertyName
     * @return  bool
     */
    protected function hasProperty($propertyName)
    {
        if (count($this->properties) === 1
          && isset($this->properties['__value']) === true
          && 'value' === $propertyName) {
            return isset($this->properties['__value']);
        }

        return isset($this->properties[$propertyName]);
    }

    /**
     * restore reflection instances
     */
    public function __wakeup()
    {
        foreach ($this->properties as $propertyName => $value) {
            if ($value instanceof BaseReflectionClass) {
                $this->properties[$propertyName] = new ReflectionClass($value->getName());
            }
        }
    }
}
?>