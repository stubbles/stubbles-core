<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
use stubbles\lang\Enum;
/**
 * Type reference for mixed types.
 *
 * @since   3.1.1
 */
class MixedType extends Enum implements ReflectionType
{
    /**
     * mixed type of type mixed
     *
     * @api
     * @type  MixedType
     */
    public static $MIXED;
    /**
     * mixed type of type object
     *
     * @api
     * @type  MixedType
     */
    public static $OBJECT;

    /**
     * static initializing
     */
    public static function __static()
    {
        self::$MIXED  = new self('mixed', true);
        self::$OBJECT = new self('object', false);
    }

    /**
     * checks whether given type name is known
     *
     * @api
     * @param   string  $name
     * @return  bool
     */
    public static function isKnown($name)
    {
        return in_array($name, ['mixed', 'object']);
    }

    /**
     * returns the enum instance of given class identified by its name
     *
     * @api
     * @param   string  $name
     * @return  ReflectionPrimitive
     */
    public static function forName($name)
    {
        return parent::forName(strtoupper($name));
    }

    /**
     * returns the name of the type
     *
     * @api
     * @return  string
     */
    public function getName()
    {
        return $this->name();
    }

    /**
     * checks whether the type is an object
     *
     * @api
     * @return  bool
     */
    public function isObject()
    {
        return true;
    }

    /**
     * checks whether the type is a primitive
     *
     * @api
     * @return  bool
     */
    public function isPrimitive()
    {
        return $this->value;
    }

    /**
     * returns a string representation of the class
     *
     * @api
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->name . "] {\n}\n";
    }
}
MixedType::__static();
