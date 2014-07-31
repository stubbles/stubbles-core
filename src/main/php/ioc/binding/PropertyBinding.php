<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
use stubbles\ioc\Injector;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
/**
 * Provides properties, partially based on current runtime mode.
 *
 * @since  3.4.0
 */
class PropertyBinding implements Binding
{
    /**
     * This string is used when generating the key for a constant binding.
     */
    const TYPE             = '__PROPERTY__';
    /**
     * actual properties
     *
     * @type  \stubbles\lang\Properties
     */
    private $properties;
    /**
     * current runtime mode
     *
     * @type  Mode
     */
    private $mode;

    /**
     * constructor
     *
     * @param  \stubbles\lang\Properties  $properties
     * @param  \stubbles\lang\Mode        $mode
     */
    public function __construct(Properties $properties, Mode $mode)
    {
        $this->properties = $properties;
        $this->mode       = $mode;
    }

    /**
     * checks if property with given name exists
     *
     * @param   string  $name
     * @return  bool
     */
    public function hasProperty($name)
    {
        if ($this->properties->containValue($this->mode->name(), $name)) {
            return true;
        }

        return $this->properties->containValue('config', $name);
    }

    /**
     * returns the created instance
     *
     * @param   \stubbles\ioc\Injector  $injector
     * @param   string    $name
     * @return  mixed
     * @throws  \stubbles\ioc\binding\BindingException
     */
    public function getInstance(Injector $injector, $name)
    {
        if ($this->properties->containValue($this->mode->name(), $name)) {
            return $this->properties->value($this->mode->name(), $name);
        }

        if ($this->properties->containValue('config', $name)) {
            return $this->properties->value('config', $name);
        }

        throw new BindingException('Missing property ' . $name . ' in runtime mode ' . $this->mode->name());
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        return self::TYPE;
    }
}
