<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\lang\reflect\ReflectionClass;
use stubbles\peer\http\HttpUri;
use stubbles\peer\MalformedUriException;
/**
 * Provides functions for parsing strings to a target type.
 *
 * @since  4.1.0
 */
class Parse
{
    /**
     * list of values which are treated as boolean true
     *
     * @type  string[]
     */
    private static $booleanTrue  = ['yes', 'true', 'on'];
    /**
     * list of known type recognitions
     *
     * @type  callable[]
     */
    private static $recognitions = [];

    /**
     * static initializer
     */
    public static function __static()
    {
        self::addRecognition(function($string) { if (self::toBool($string)) { return true; } }, 'booleanTrue');
        self::addRecognition(function($string) { if (in_array(strtolower($string), ['no', 'false', 'off'])) { return false; } }, 'booleanFalse');
        self::addRecognition(function($string) { if (preg_match('/^[+-]?[0-9]+$/', $string) != false) { return self::toInt($string);} }, 'int');
        self::addRecognition(function($string) { if (preg_match('/^[+-]?[0-9]+\.[0-9]+$/', $string) != false) { return self::toFloat($string); } }, 'float');
        self::addRecognition(
                function($string)
                {
                    if (substr($string, 0, 1) === '[' && substr($string, -1) === ']') {
                        return (strstr($string, ':') !== false) ? self::toMap($string) : self::toList($string);
                    }
                },
                'array'
        );
        self::addRecognition(function($string) { if (strstr($string, '..') !== false) { return self::toRange($string); } }, 'range');
        self::addRecognition(
                function($string)
                {
                    if (substr($string, 0, 4) === 'http') {
                        try {
                            return HttpUri::fromString($string);
                        } catch (MalformedUriException $murle) { }
                    }

                    return;

                },
                'stubbles\peer\http\HttpUri'
        );
        self::addRecognition(function($string) { $class = self::toClass($string); if (null !== $class) { return $class; } }, 'stubbles\lang\reflect\ReflectionClass');
        self::addRecognition(function($string) { $enum = self::toEnum($string); if (null !== $enum) { return $enum; } }, 'stubbles\lang\Enum');
        self::addRecognition(function($string) { if (defined($string)) { return constant($string); } }, 'constant');
    }

    /**
     * adds given callable for type recognition
     *
     * The callable must accept a string value and return a type. If the return
     * value is null the recognition will be treated as failed and the next
     * recognition will be tried.
     * <code>
     * Parse::addRecognition(
     *      function($string)
     *      {
     *          if ('Binford 6100' === $string) {
     *              return new Binford('More power!');
     *          }
     *      },
     *      'binford'
     * );
     * Parse::toType('Binford 6100'); // yields instance of Binford
     * </code>
     *
     * @param  callable  $recognition
     * @param  string    $name         name under which recognition should be stored
     */
    public static function addRecognition(callable $recognition, $name)
    {
        self::$recognitions[$name] = $recognition;
    }

    /**
     * removes recognition with given name
     *
     * @param   string  $name  name under which recognition is stored
     * @return  bool  true if recognition was present and removed, false otherwise
     */
    public static function removeRecognition($name)
    {
        if (isset(self::$recognitions[$name])) {
            unset(self::$recognitions[$name]);
            return true;
        }

        return false;
    }

    /**
     * parses string to a type depending on the value of the string
     *
     * These are the conversions being tried in their order:
     * String value                                         => result
     * null, ''                                             => string value as it is
     * 'null'                                               => null
     * 'yes', 'true', 'on'                                  => true
     * 'no', 'false', 'off'                                 => false
     * string containing of numbers only                    => integer
     * string containing of numbers and a dot               => float
     * string starting with [, ending with ]
     *      and containing at least one :                   => map   (i.e. array, see toMap())
     * string starting with [, ending with ]                => list  (i.e. array, see toList())
     * string containing ..                                 => range (i.e. array, see toRange())
     * string containing a valid HTTP uri                   => stubbles\peer\http\HttpUri
     * <fully\qualified\Classname.class>                    => stubbles\lang\reflect\ReflectionClass
     * <fully\qualified\Classname::$enumName>               => stubbles\lang\Enum
     * string containing name of a constant                 => value of the constant
     * recognition added via Parse::addRecognition()        => return type of the callable
     * all other                                            => string value as is
     *
     * @param   string   $string     the value to convert
     * @return  mixed
     */
    public static function toType($string)
    {
        if (null == $string) {
            return $string;
        }

        if ('null' === strtolower($string)) {
            return null;
        }

        foreach (self::$recognitions as $recognition) {
            $value = $recognition($string);
            if (null !== $value) {
                return $value;
            }
        }

        return (string) $string;
    }

    /**
     * parses string to an integer
     *
     * @param   string  $string
     * @return  int
     */
    public static function toInt($string)
    {
        return intval($string);
    }

    /**
     * parses string to a float
     *
     * @param   string  $string
     * @return  float
     */
    public static function toFloat($string)
    {
        return floatval($string);
    }

    /**
     * parses string to a boolean value
     *
     * The return value is true if the string value is either "1", "yes", "true"
     * or "on". In any other case the return value will be false.
     *
     * @param   string  $string
     * @return  bool
     */
    public static function toBool($string)
    {
        return in_array(strtolower($string), self::$booleanTrue);
    }

    /**
     * parses string to a list of strings
     *
     * If the value is empty the return value will be an empty array. If the
     * value is not empty it will be splitted at "|".
     * Example:
     * <code>
     * key = "foo|bar|baz"
     * </code>
     * The resulting array would be ['foo', 'bar', 'baz']
     *
     * @param   string  $string
     * @return  string[]
     */
    public static function toList($string)
    {
        $withoutParenthesis = self::removeParenthesis($string);
        if (empty($withoutParenthesis)) {
            return [];
        }


        if (strstr($withoutParenthesis, '|') !== false) {
            return explode('|', $withoutParenthesis);
        }

        return [$withoutParenthesis];
    }

    /**
     * removes leading and trailing parenthesis from list and map strings
     *
     * @param   string  $string
     * @return  string
     */
    private static function removeParenthesis($string)
    {
        if (substr($string, 0, 1) === '[' && substr($string, -1) === ']') {
            return substr($string, 1, strlen($string) - 2);
        }

        return $string;
    }

    /**
     * parses string to a map
     *
     * If the value is empty the return value will be an empty map. If the
     * value is not empty it will be splitted at "|". The resulting list will
     * be splitted at the first ":", the first part becoming the key and the rest
     * becoming the value in the map. If no ":" is present, the whole value will
     * be appended to the map using a numeric value for the key.
     * Example:
     * <code>
     * key = "foo:bar|baz"
     * </code>
     * The resulting map would be ['foo' => 'bar', 'baz']
     *
     * @param   string  $string
     * @return  array
     */
    public static function toMap($string)
    {
        if (empty($string)) {
            return [];
        }

        $map = [];
        foreach (self::toList($string) as $keyValue) {
            if (strstr($keyValue, ':') !== false) {
                list($key, $value) = explode(':', $keyValue, 2);
                $map[$key]         = $value;
            } else {
                $map[] = $keyValue;
            }
        }

        return $map;
    }

    /**
     * parses string to a range
     *
     * Ranges can be written as 1..5 which will return an array: [1, 2, 3, 4, 5].
     * Works also with letters and reverse order a..e, e..a and 5..1.
     *
     * @param   string  $string
     * @return  mixed[]
     */
    public static function toRange($string)
    {
        if (empty($string)) {
            return [];
        }

        if (!strstr($string, '..')) {
            return [];
        }

        list($min, $max) = explode('..', $string, 2);
        if (null == $min || null == $max) {
            return [];
        }

        return range($min, $max);
    }

    /**
     * parses string to a reflection class
     *
     * String must have the format <fully\qualified\Classname.class>. In case
     * the string can not be parsed the return value is null.
     *
     * @param   string  $string
     * @return  \stubbles\lang\reflect\ReflectionClass
     */
    public static function toClass($string)
    {
        $classnameMatches = [];
        if (preg_match('/^([a-zA-Z_]{1}[a-zA-Z0-9_\\\\]*)\.class/', $string, $classnameMatches) != false) {
            return new ReflectionClass($classnameMatches[1]);
        }

        return null;
    }

    /**
     * parses string to an enum instance
     *
     * String must have the format <fully\qualified\Classname::$enumName>. In
     * case the string can not be parsed the return value is null.
     *
     * @param   string  $string
     * @return  \stubbles\lang\Enum
     */
    public static function toEnum($string)
    {
        $enumMatches = [];
        if (preg_match('/^([a-zA-Z_]{1}[a-zA-Z0-9_\\\\]*)::\$([a-zA-Z_]{1}[a-zA-Z0-9_]*)/', $string, $enumMatches) != false) {
            $enumClassName = $enumMatches[1];
            $instanceName  = $enumMatches[2];
            return $enumClassName::forName($instanceName);
        }

        return null;
    }

    /**
     * a value to parse
     *
     * @type  string
     */
    private $value;

    /**
     * constructor
     *
     * @param  string  $value
     * @since  5.0.0
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * returns value as string (i.e., a pass through)
     *
     * @return  string
     * @since   5.0.0
     */
    public function asString()
    {
        return $this->value;
    }

    /**
     * parses initial value as integer
     *
     * @return  int
     * @since   5.0.0
     */
    public function asInt()
    {
        return self::toInt($this->value);
    }

    /**
     * parses initial value as float
     *
     * @return  float
     * @since   5.0.0
     */
    public function asFloat()
    {
        return self::toFloat($this->value);
    }

    /**
     * parses initial value as bool
     *
     * @return  bool
     * @since   5.0.0
     */
    public function asBool()
    {
        return self::toBool($this->value);
    }

    /**
     * parses initial value as list
     *
     * @return  string[]
     * @since   5.0.0
     */
    public function asList()
    {
        return self::toList($this->value);
    }

    /**
     * parses initial value as list
     *
     * @return  array
     * @since   5.0.0
     */
    public function asMap()
    {
        return self::toMap($this->value);
    }

    /**
     * parses initial value as range
     *
     * @return  mixed[]
     * @since   5.0.0
     */
    public function asRange()
    {
        return self::toRange($this->value);
    }

    /**
     * parses initial value as reflection class
     *
     * @return  \stubbles\lang\reflect\ReflectionClass
     * @since   5.0.0
     */
    public function asClass()
    {
        return self::toClass($this->value);
    }

    /**
     * parses initial value as enum instance
     *
     * @return  \stubbles\lang\Enum
     * @since   5.0.0
     */
    public function asEnum()
    {
        return self::toEnum($this->value);
    }
}
Parse::__static();
