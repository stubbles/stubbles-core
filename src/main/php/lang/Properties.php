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
use stubbles\lang\exception\FileNotFoundException;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\IOException;
/**
 * Class to read and parse properties.
 *
 * Properties are iterable using foreach:
 * <code>
 * foreach (Properties::fromFile($propertyFile) as $sectionName => $section) {
 *     // $section is an array containing all section values as key-value pairs
 * }
 * </code>
 */
class Properties implements \Iterator
{
    /**
     * property data
     *
     * @type  array
     */
    protected $propertyData;

    /**
     * constructor
     *
     * @api
     * @param  array  $propertyData  the property data
     */
    public function __construct(array $propertyData = [])
    {
        $this->propertyData = $propertyData;
    }

    /**
     * construct class from string
     *
     * @api
     * @param   string  $propertyString
     * @return  Properties
     * @throws  IllegalArgumentException
     * @since   2.0.0
     */
    public static function fromString($propertyString)
    {
        $propertyData = @parse_ini_string($propertyString, true);
        if (false === $propertyData) {
            throw new IllegalArgumentException('Property string contains errors and can not be parsed.');
        }

        return new static($propertyData);
    }

    /**
     * construct class from a file
     *
     * @api
     * @param   string  $propertiesFile  full path to file containing properties
     * @return  Properties
     * @throws  FileNotFoundException  if file can not be found or is not readable
     * @throws  IOException            if file contains errors and can not be parsed
     */
    public static function fromFile($propertiesFile)
    {
        if (!file_exists($propertiesFile) || !is_readable($propertiesFile)) {
            throw new FileNotFoundException($propertiesFile);
        }

        $propertyData = @parse_ini_file($propertiesFile, true);
        if (false === $propertyData) {
            throw new IOException('Property file at ' . $propertiesFile . ' contains errors and can not be parsed.');
        }

        return new static($propertyData);
    }

    /**
     * merges properties from another instance into itself
     *
     * The return value is a new instance with properties from this and the other
     * instance combined. If both instances have sections of the same name the
     * section from the other instance overwrite the section from this instance.
     *
     * @api
     * @param   Properties  $otherProperties
     * @return  Properties
     * @since   1.3.0
     */
    public function merge(Properties $otherProperties)
    {
        return new static(array_merge($this->propertyData, $otherProperties->propertyData));
    }

    /**
     * returns a list of section keys
     *
     * @api
     * @return  string[]
     * @deprecated  since 4.0.0, iterate over instance instead, will be removed with 5.0.0
     */
    public function getSections()
    {
        return array_keys($this->propertyData);
    }

    /**
     * checks if a certain section exists
     *
     * @api
     * @param   string  $section  name of the section
     * @return  bool
     */
    public function hasSection($section)
    {
        return isset($this->propertyData[$section]);
    }

    /**
     * returns a whole section if it exists or the default if the section does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   array   $default  value to return if section does not exist
     * @return  scalar[]
     * @since   4.0.0
     */
    public function section($section, array $default = [])
    {
        if (isset($this->propertyData[$section])) {
            return $this->propertyData[$section];
        }

        return $default;
    }

    /**
     * returns a whole section if it exists or the default if the section does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   array   $default  value to return if section does not exist
     * @return  scalar[]
     * @deprecated  since 4.0.0, use section() instead, will be removed with 5.0.0
     */
    public function getSection($section, array $default = [])
    {
        if (isset($this->propertyData[$section])) {
            return $this->propertyData[$section];
        }

        return $default;
    }

    /**
     * returns a list of all keys of a specific section
     *
     * @api
     * @param   string    $section  name of the section
     * @param   string[]  $default  value to return if section does not exist
     * @return  string[]
     * @since   4.0.0
     */
    public function keysForSection($section, array $default = [])
    {
        if (isset($this->propertyData[$section])) {
            return array_keys($this->propertyData[$section]);
        }

        return $default;
    }

    /**
     * returns a list of all keys of a specific section
     *
     * @api
     * @param   string    $section  name of the section
     * @param   string[]  $default  value to return if section does not exist
     * @return  string[]
     * @deprecated  since 4.0.0, use keysForSection() instead, will be removed with 5.0.0
     */
    public function getSectionKeys($section, array $default = [])
    {
        return $this->keysForSection($section, $default);
    }

    /**
     * checks if a certain section contains a certain key
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @return  bool
     */
    public function hasValue($section, $key)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return true;
        }

        return false;
    }

    /**
     * returns a value from a section or a default value if the section or key does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   mixed   $default  value to return if section or key does not exist
     * @return  scalar
     * @since   4.0.0
     */
    public function value($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return $this->propertyData[$section][$key];
        }

        return $default;
    }

    /**
     * returns a value from a section or a default value if the section or key does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   mixed   $default  value to return if section or key does not exist
     * @return  scalar
     * @deprecated  since 4.0.0, use value() instead, will be removed with 5.0.0
     */
    public function getValue($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return $this->propertyData[$section][$key];
        }

        return $default;
    }

    /**
     * returns a string from a section or a default string if the section or key does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   string  $default  string to return if section or key does not exist
     * @return  string
     */
    public function parseString($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return (string) $this->propertyData[$section][$key];
        }

        return $default;
    }

    /**
     * returns an integer or a default value if the section or key does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   int     $default  value to return if section or key does not exist
     * @return  int
     */
    public function parseInt($section, $key, $default = 0)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return intval($this->propertyData[$section][$key]);
        }

        return $default;
    }

    /**
     * returns a float or a default value if the section or key does not exist
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   float   $default  value to return if section or key does not exist
     * @return  float
     */
    public function parseFloat($section, $key, $default = 0.0)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            return floatval($this->propertyData[$section][$key]);
        }

        return $default;
    }

    /**
     * returns a boolean or a default value if the section or key does not exist
     *
     * The return value is true if the property value is either "1", "yes",
     * "true" or "on". In any other case the return value will be false.
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   bool    $default  value to return if section or key does not exist
     * @return  bool
     */
    public function parseBool($section, $key, $default = false)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            $val = $this->propertyData[$section][$key];
            return ('1' == $val || 'yes' === $val || 'true' === $val || 'on' === $val);
        }

        return $default;
    }

    /**
     * returns an array from a section or a default array if the section or key does not exist
     *
     * If the value is empty the return value will be an empty array. If the
     * value is not empty it will be splitted at "|".
     * Example:
     * <code>
     * key = "foo|bar|baz"
     * </code>
     * The resulting array would be ['foo', 'bar', 'baz']
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  array to return if section or key does not exist
     * @return  array
     */
    public function parseArray($section, $key, array $default = null)
    {
        if (!isset($this->propertyData[$section]) || !isset($this->propertyData[$section][$key])) {
            return $default;
        }

        if (empty($this->propertyData[$section][$key])) {
            return [];
        }

        return explode('|', $this->propertyData[$section][$key]);
    }

    /**
     * returns a hash from a section or a default hash if the section or key does not exist
     *
     * If the value is empty the return value will be an empty hash. If the
     * value is not empty it will be splitted at "|". The resulting array will
     * be splitted at the first ":", the first part becoming the key and the rest
     * becoming the value in the hash. If no ":" is present, the whole value will
     * be appended to the hash using a numeric value.
     * Example:
     * <code>
     * key = "foo:bar|baz"
     * </code>
     * The resulting hash would be ['foo' => 'bar', 'baz']
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  array to return if section or key does not exist
     * @return  array
     */
    public function parseHash($section, $key, array $default = null)
    {
        if (!isset($this->propertyData[$section]) || !isset($this->propertyData[$section][$key])) {
            return $default;
        }

        if (empty($this->propertyData[$section][$key])) {
            return [];
        }

        $hash = [];
        foreach (explode('|', $this->propertyData[$section][$key]) as $keyValue) {
            if (strstr($keyValue, ':') !== false) {
                list($key, $value) = explode(':', $keyValue, 2);
                $hash[$key]        = $value;
            } else {
                $hash[] = $keyValue;
            }
        }

        return $hash;
    }

    /**
     * returns an array containing values from min to max of the range or a default if the section or key does not exist
     *
     * Ranges in properties should be written as
     * <code>
     * key = 1..5
     * </code>
     * This will return an array: [1, 2, 3, 4, 5]
     * Works also with letters and reverse order:
     * <code>
     * letters = a..e
     * letter_reverse = e..a
     * numbers_reverse = 5..1
     * </code>
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  range to return if section or key does not exist
     * @return  array
     */
    public function parseRange($section, $key, array $default = [])
    {
        if (!isset($this->propertyData[$section]) || !isset($this->propertyData[$section][$key])) {
            return $default;
        }

        if (!strstr($this->propertyData[$section][$key], '..')) {
            return [];
        }

        list($min, $max) = explode('..', $this->propertyData[$section][$key]);
        if (null == $min || null == $max) {
            return [];
        }

        return range($min, $max);
    }

    /**
     * returns current section
     *
     * @return  array
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function current()
    {
        return current($this->propertyData);
    }

    /**
     * returns name of current section
     *
     * @return  string
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function key()
    {
        return key($this->propertyData);
    }

    /**
     * forwards to next section
     *
     * @see  http://php.net/manual/en/spl.iterators.php
     */
    public function next()
    {
        next($this->propertyData);
    }

    /**
     * rewind to first section
     *
     * @see  http://php.net/manual/en/spl.iterators.php
     */
    public function rewind()
    {
        reset($this->propertyData);
    }

    /**
     * checks if there are more valid sections
     *
     * @return  bool
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function valid()
    {
        return current($this->propertyData);
    }
}
