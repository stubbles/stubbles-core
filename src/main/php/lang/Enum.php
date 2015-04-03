<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;;;
/**
 * Base class for enums.
 */
abstract class Enum
{
    /**
     * name of the enum
     *
     * @type  string
     */
    protected $name;
    /**
     * value of enum
     *
     * @type  int
     */
    protected $value;

    /**
     * constructor
     *
     * @param  string  $name   enum name
     * @param  mixed   $value  enum value
     */
    protected function __construct($name, $value = null)
    {
        $this->name  = $name;
        $this->value = ((null !== $value) ? ($value) : ($name));
    }

    /**
     * forbid cloning of enums
     *
     * @throws  \RuntimeException
     */
    public final function __clone()
    {
        throw new \RuntimeException('Cloning of enums is not allowed.');
    }

    /**
     * returns the name of the enum
     *
     * @api
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * returns the value of the enum
     *
     * @api
     * @return  mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * returns the enum instance of given class identified by its name
     *
     * @api
     * @param   string  $name
     * @return  \stubbles\lang\Enum
     * @throws  \InvalidArgumentException
     */
    public static function forName($name)
    {
        $enum = new \ReflectionClass(get_called_class());
        try {
            return $enum->getStaticPropertyValue($name);
        } catch (\ReflectionException $re) {
            throw new \InvalidArgumentException($re->getMessage());
        }
    }

    /**
     * returns the enum instance of given class identified by its value
     *
     * @api
     * @param   string  $value
     * @return  \stubbles\lang\Enum
     * @throws  \InvalidArgumentException
     */
    public static function forValue($value)
    {
        $enumClass = new \ReflectionClass(get_called_class());
        foreach ($enumClass->getStaticProperties() as $instance) {
            if ($instance->value() === $value) {
                return $instance;
            }
        }

        throw new \InvalidArgumentException(
                'Enum ' . $enumClass->getName() . ' for value ' . $value
                . ' does not exist.'
        );
    }

    /**
     * returns a list of all instances for given enum
     *
     * @api
     * @return  \stubbles\lang\Enum[]
     */
    public static function instances()
    {
        $enum = new \ReflectionClass(get_called_class());
        return array_values($enum->getStaticProperties());
    }

    /**
     * returns a list of enum names for given enum
     *
     * @api
     * @return  string[]
     */
    public static function namesOf()
    {
        $enum = new \ReflectionClass(get_called_class());
        return array_keys($enum->getStaticProperties());
    }

    /**
     * returns a list of values for given enum
     *
     * @api
     * @return  mixed[]
     */
    public static function valuesOf()
    {
        $enum   = new \ReflectionClass(get_called_class());
        $values = [];
        foreach ($enum->getStaticProperties() as $name => $instance) {
            $values[$name] = $instance->value;
        }

        return $values;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @XmlIgnore
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return (get_class($compare) === get_class($this) && $compare->name() === $this->name);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        $string  = get_class($this) . " {\n";
        $string .= '    ' . $this->name . "\n";
        $string .= '    ' . $this->value . "\n";
        $string .= "}\n";
        return $string;
    }
}
