<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use net\stubbles\ioc\binding\BindingException;
use net\stubbles\lang\Mode;
use net\stubbles\lang\Properties;
/**
 * Provides properties based on current runtime mode.
 *
 * @since  3.4.0
 */
class RuntimeModePropertiesProvider implements InjectionProvider
{
    /**
     * actual properties
     *
     * @type  Properties
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
     * @param  Properties  $properties
     * @Inject
     * @Named('config')
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * sets runtime mode
     *
     * @param   Mode  $mode
     * @return  RuntimeModePropertiesProvider
     * @Inject(optional=true)
     */
    public function setMode(Mode $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     * @throws  BindingException
     */
    public function get($name = null)
    {
        $runtimeModeName = $this->getRuntimeModeName();
        if (null !== $runtimeModeName && $this->properties->hasValue($runtimeModeName, $name)) {
            return $this->properties->getValue($runtimeModeName, $name);
        }

        if ($this->properties->hasValue('common', $name)) {
            return $this->properties->getValue('common', $name);
        }

        throw new BindingException('Missing property ' . $name . (null !== $this->mode ? ' in runtime mode ' . $this->mode->name() : ''));
    }

    /**
     * returns name of current runtime mode
     *
     * @return  string
     */
    private function getRuntimeModeName()
    {
        if (null === $this->mode) {
            return null;
        }

        return strtolower($this->mode->name());
    }
}
