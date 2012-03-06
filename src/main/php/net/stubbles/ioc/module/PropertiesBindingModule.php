<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\module;
use net\stubbles\ioc\Binder;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\Properties;
/**
 * Module to read properties from a file and bind them.
 */
class PropertiesBindingModule extends BaseObject implements BindingModule
{
    /**
     * different path types
     *
     * @type  string[]
     */
    private $pathTypes   = array('cache',
                                 'config'
                           );
    /**
     * path to project files
     *
     * @type  string
     */
    private $projectPath;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;

    }

    /**
     * static constructor
     *
     * @param   string  $projectPath  path to project
     * @return  PropertiesBindingModule
     */
    public static function create($projectPath)
    {
        return new self($projectPath);
    }

    /**
     * adds a path type to be bound
     *
     * The path type will lead to a path available via injection. The constant
     * name of this path will be <i>net.stubbles.$pathtype.path</i> and it's
     * value will be $projectPath/$pathtype.
     *
     * @api
     * @param   string  $pathType
     * @return  PropertiesBindingModule
     */
    public function addPathType($pathType)
    {
        $this->pathTypes[] = $pathType;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $configPath = null;
        $binder->bindConstant('net.stubbles.project.path')
               ->to($this->projectPath);
        foreach ($this->buildPathes($this->projectPath) as $name => $value) {
            $binder->bindConstant($name)
                   ->to($value);
            if ('net.stubbles.config.path' === $name) {
                $configPath = $value;
            }
        }

        if (!$this->propertiesAvailable($configPath)) {
            return;
        }

        $properties = $this->getProperties($configPath);
        $binder->bind('net\\stubbles\\lang\\Properties')
               ->named('config')
               ->toInstance($properties);
        foreach ($properties->getSection('config') as $key => $value) {
            $binder->bindConstant($key)
                   ->to($value);
        }
    }

    /**
     * appends directory separator if necessary
     *
     * @param   string  $path
     * @return  string
     */
    private function buildPathes($path)
    {
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $pathes = array();
        foreach ($this->pathTypes as $pathType) {
            $pathes['net.stubbles.' . $pathType . '.path'] = $path . $pathType;
        }

        return $pathes;
    }

    /**
     * checks whether properties are available at given path
     *
     * @param   string  $configPath
     * @return  bool
     */
    private function propertiesAvailable($configPath)
    {
        return !empty($configPath) && file_exists($configPath . DIRECTORY_SEPARATOR . 'config.ini');
    }

    /**
     * returns list of properties
     *
     * @param   string  $configPath
     * @return  Properties
     */
    private function getProperties($configPath)
    {
        return Properties::fromFile($configPath . DIRECTORY_SEPARATOR . 'config.ini');
    }
}
?>