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
 * Properties instance which allows modification of properties.
 *
 * @since       1.7.0
 */
class ModifiableProperties extends Properties
{
    /**
     * construct class from string
     *
     * @api
     * @param   string  $propertyString
     * @return  ModifiableProperties
     * @throws  IllegalArgumentException
     * @since   2.0.0
     */
    public static function fromString($propertyString)
    {
        $propertyData = @parse_ini_string($propertyString, true);
        if (false === $propertyData) {
            throw new IllegalArgumentException('Property string contains errors and can not be parsed.');
        }

        return new self($propertyData);
    }

    /**
     * construct class from a file
     *
     * @api
     * @param   string  $propertiesFile  full path to file containing properties
     * @return  ModifiableProperties
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

        return new self($propertyData);
    }

    /**
     * sets a section
     *
     * If a section with this name already exists it will be replaced.
     *
     * @api
     * @param   string  $section
     * @param   array   $data
     * @return  ModifiableProperties
     */
    public function setSection($section, array $data)
    {
        $this->propertyData[$section] = $data;
        return $this;
    }

    /**
     * sets value of property in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @api
     * @param   string  $section
     * @param   string  $name
     * @param   mixed   $value
     * @return  ModifiableProperties
     */
    public function setValue($section, $name, $value)
    {
        if (!isset($this->propertyData[$section])) {
            $this->propertyData[$section] = [];
        }

        $this->propertyData[$section][$name] = (string) $value;
        return $this;
    }

    /**
     * sets a boolean property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @api
     * @param   string  $section
     * @param   string  $name
     * @param   bool    $value
     * @return  ModifiableProperties
     */
    public function setBooleanValue($section, $name, $value)
    {
        return $this->setValue($section, $name, ((true === $value) ? ('true') : ('false')));
    }

    /**
     * sets an array as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @api
     * @param   string  $section
     * @param   string  $name
     * @param   array   $value
     * @return  ModifiableProperties
     */
    public function setArrayValue($section, $name, array $value)
    {
        return $this->setValue($section, $name, join('|', $value));
    }

    /**
     * sets a hash map as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @api
     * @param   string  $section
     * @param   string  $name
     * @param   array   $hash
     * @return  ModifiableProperties
     */
    public function setHashValue($section, $name, array $hash)
    {
        $values = [];
        foreach($hash as $key => $val) {
            $values[] = $key . ':' . $val;
        }

        return $this->setArrayValue($section, $name, $values);
    }

    /**
     * sets a range as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @api
     * @param   string  $section
     * @param   string  $name
     * @param   array   $range
     * @return  ModifiableProperties
     */
    public function setRangeValue($section, $name, array $range)
    {
        return $this->setValue($section, $name, array_shift($range) . '..' . array_pop($range));
    }
}
