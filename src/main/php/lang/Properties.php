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
use stubbles\lang\exception\IllegalAccessException;
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
        foreach ($propertyData as $section => $values) {
            foreach (array_keys($values) as $key) {
                if (substr($key, -8) === 'password') {
                    $propertyData[$section][$key] = SecureString::create($values[$key]);
                }
            }
        }

        $this->propertyData = $propertyData;
    }

    /**
     * construct class from string
     *
     * @api
     * @param   string  $propertyString
     * @return  \stubbles\lang\Properties
     * @throws  \stubbles\lang\exception\IllegalArgumentException
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
     * @return  \stubbles\lang\Properties
     * @throws  \stubbles\lang\exception\FileNotFoundException  if file can not be found or is not readable
     * @throws  \stubbles\lang\exception\IOException            if file contains errors and can not be parsed
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
     * @param   \stubbles\lang\Properties  $otherProperties
     * @return  \stubbles\lang\Properties
     * @since   1.3.0
     */
    public function merge(Properties $otherProperties)
    {
        return new static(array_merge($this->propertyData, $otherProperties->propertyData));
    }

    /**
     * checks if a certain section exists
     *
     * @api
     * @param   string  $section  name of the section
     * @return  bool
     * @since   4.0.0
     */
    public function containSection($section)
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
     * checks if a certain section contains a certain key
     *
     * @api
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @return  bool
     * @since   4.0.0
     */
    public function containValue($section, $key)
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
     * parses value and returns the parsing result
     *
     * @param   string  $section
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     * @see     \stubbles\lang\Parse::toType()
     * @since   4.1.0
     */
    public function parseValue($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) && isset($this->propertyData[$section][$key])) {
            if ($this->propertyData[$section][$key] instanceof SecureString) {
                return $this->propertyData[$section][$key];
            }

            return Parse::toType($this->propertyData[$section][$key]);
        }

        return $default;
    }

    /**
     * returns a parser instance for the value
     *
     * In case the value was recognized as password and is therefore an instance
     * of \stubbles\lang\SecureString  an IllegalAccessException is thrown as
     * this value can not be parsed.
     *
     * @param   string  $section
     * @param   string  $key
     * @return  \stubbles\lang\Parse
     * @throws  \stubbles\lang\exception\IllegalAccessException
     * @since   5.0.0
     */
    public function parse($section, $key)
    {
        if (!isset($this->propertyData[$section]) || !isset($this->propertyData[$section][$key])) {
            return new Parse(null);
        }

        if ($this->propertyData[$section][$key] instanceof SecureString) {
            throw new IllegalAccessException('Can not parse fields with passwords');
        }

        return new Parse($this->propertyData[$section][$key]);
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
