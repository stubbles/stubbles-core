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
/**
 * Represents an annotation on the code.
 */
class Annotation
{
    /**
     * name of annotation
     *
     * @type  string
     */
    private $name;
    /**
     * values of annotation
     *
     * @type  array
     */
    private $values     = [];
    /**
     * target from which annotation was retrieved
     *
     * @type  string
     */
    private $target;
    /**
     * original annotation type
     *
     * @type  string
     */
    private $type;

    /**
     * constructor
     *
     * @param  string  $name    name of annotation, in case of casted annotations it the casted type
     * @param  string  $target  name of target where annotation is for, i.e. the class, method, function, property or parameter
     * @param  array   $values  optional  map of all annotation values
     * @param  string  $type    optional  type of annotation in case $name reflects a casted type
     */
    public function __construct($name, $target, array $values = [], $type = null)
    {
        $this->name   = $name;
        $this->target = $target;
        $this->values = $values;
        $this->type   = (null === $type) ? $name : $type;
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
     * @return  string
     * @since   4.0.0
     */
    public function target()
    {
        return $this->target;
    }

    /**
     *
     * @return  string
     * @since   5.0.0
     */
    public function type()
    {
        return $this->type;
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
        return isset($this->values[$name]);
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
        if (isset($this->values[$name])) {
            return $this->parseType($this->values[$name]);
        }

        return null;
    }

    /**
     * returns a parser instance for the value
     *
     * Actual call the parsing methods on the parser returns null if a value
     * with given name does not exist or is not set.
     *
     * @api
     * @param   string  $name
     * @return  \stubbles\lang\Parse
     * @since   5.0.0
     */
    public function parse($name)
    {
        if (isset($this->values[$name])) {
            return new Parse($this->values[$name]);
        }

        return new Parse(null);
    }

    /**
     * responds to a method call of an undefined method
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     * @throws  \BadMethodCallException
     */
    public function  __call($name, $arguments)
    {
        if (isset($this->values[$name])) {
            return $this->parseType($this->values[$name]);
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

        throw new \BadMethodCallException('The method ' . $name . ' does not exit.');
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
        if (count($this->values) === 1 && isset($this->values['__value'])) {
            return $this->parseType($this->values['__value']);
        }

        if (isset($this->values[$propertyName])) {
            return $this->parseType($this->values[$propertyName]);
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
        if (count($this->values) === 1 && isset($this->values['__value'])) {
            return Parse::toBool($this->values['__value']);
        }

        if (isset($this->values[$propertyName])) {
            return Parse::toBool($this->values[$propertyName]);
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
        if (count($this->values) === 1
          && isset($this->values['__value'])
          && 'value' === $propertyName) {
            return isset($this->values['__value']);
        }

        return isset($this->values[$propertyName]);
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
