<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
use stubbles\lang;
use stubbles\lang\Parse;
use stubbles\lang\exception\MethodNotSupportedException;
/**
 * Interface for an annotation.
 */
class Annotation
{
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
    protected $properties = [];
    /**
     * name of annotation target
     *
     * @type  string
     */
    private $targetName;

    /**
     * constructor
     *
     * @param  string  $name
     * @param  string  $targetName
     * @param  array   $values      optional  map of all annotation values
     */
    public function __construct($name, $targetName, array $values = [])
    {
        $this->name       = $name;
        $this->targetName = $targetName;
        $this->properties = $values;
    }

    /**
     * Returns the name under which the annotation is stored.
     *
     * @api
     * @return  string
     */
    public function getAnnotationName()
    {
        return $this->name;
    }

    /**
     * returns name of target where annotation is for, i.e. the class, method, function, property or parameter
     *
     * @api
     * @return  string
     * @since   4.0.0
     */
    public function targetName()
    {
        return $this->targetName;
    }

    /**
     * checks whether a value with given name exists
     *
     * Returns null if a value with given name does not exist or is not set.
     *
     * @api
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
     * @api
     * @param   string  $name
     * @return  mixed
     * @since   1.7.0
     */
    public function getValueByName($name)
    {
        if (isset($this->properties[$name])) {
            return $this->parseType($this->properties[$name]);
        }

        return null;
    }

    /**
     * sets value for given property
     *
     * @param  string  $name
     * @param  mixed   $value
     * @deprecated  since 4.2.0, annotations should be read only, will be removed with 5.0.0
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
        if (isset($this->properties[$name])) {
            return $this->parseType($this->properties[$name]);
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
        if (count($this->properties) === 1 && isset($this->properties['__value'])) {
            return $this->parseType($this->properties['__value']);
        }

        if (isset($this->properties[$propertyName])) {
            return $this->parseType($this->properties[$propertyName]);
        }

        return $defaultValue;
    }

    /**
     * parses value to correct type
     *
     * @param   string  $value
     * @return  mixed
     */
    private function parseType($value)
    {
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            return substr($value, 1, strlen($value) - 2);
        }

        return Parse::toType($value);
    }

    /**
     * returns boolean property which is retrieved via is$PROPERTYNAME()
     *
     * @param   string  $propertyName
     * @return  bool
     */
    protected function getBooleanProperty($propertyName)
    {
        if (count($this->properties) === 1 && isset($this->properties['__value'])) {
            return Parse::toBool($this->properties['__value']);
        }

        if (isset($this->properties[$propertyName])) {
            return Parse::toBool($this->properties[$propertyName]);
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
          && isset($this->properties['__value'])
          && 'value' === $propertyName) {
            return isset($this->properties['__value']);
        }

        return isset($this->properties[$propertyName]);
    }

    /**
     * returns a string representation of the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return lang\__toString($this);
    }
}
