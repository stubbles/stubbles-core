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
    private $pathTypes       = array('cache',
                                     'config',
                                     'log'
                               );
    /**
     * path to project files
     *
     * @type  string
     */
    private $projectPath;
    /**
     * current working directory
     *
     * @type  array
     */
    private $otherProperties = array();

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
     * ensures current working directory gets bound
     *
     * Will be bound to name <net.stubbles.cwd>.
     *
     * @api
     * @return  PropertiesBindingModule
     * @since   2.1.0
     */
    public function withCurrentWorkingDirectory()
    {
        $this->otherProperties['net.stubbles.cwd'] = getcwd();
        return $this;
    }

    /**
     * ensures hostname gets bound in non- and fully qualified form
     *
     * Will be bound to name <net.stubbles.hostname.nq> (non qualified) and
     * <net.stubbles.hostname.fq> (fully qualified).
     *
     * @api
     * @return  PropertiesBindingModule
     * @since   2.1.0
     */
    public function withHostname()
    {
        if (extension_loaded('posix')) {
            $uname  = posix_uname();
            $this->otherProperties['net.stubbles.hostname.nq'] = $uname['nodename'];
            $this->otherProperties['net.stubbles.hostname.fq'] = $uname['nodename'];
            if (isset($uname['domainname'])) {
                $this->otherProperties['net.stubbles.hostname.fq'] .= '.' . $uname['domainname'];
            }
        } elseif (DIRECTORY_SEPARATOR === '\\') {
            $this->otherProperties['net.stubbles.hostname.nq'] = $_SERVER['COMPUTERNAME'];
            $this->otherProperties['net.stubbles.hostname.fq'] = $_SERVER['COMPUTERNAME'];
            if (isset($_SERVER['USERDNSDOMAIN'])) {
                $this->otherProperties['net.stubbles.hostname.fq'] .= '.' . $_SERVER['USERDNSDOMAIN'];
            }
        } else {
            $this->otherProperties['net.stubbles.hostname.nq'] = php_uname('n');
            $this->otherProperties['net.stubbles.hostname.fq'] = exec('hostname -f');
        }

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

        if ($this->propertiesAvailable($configPath)) {
            $properties = $this->getProperties($configPath);
            $binder->bind('net\stubbles\lang\Properties')
                   ->named('config')
                   ->toInstance($properties);
            foreach ($properties->getSection('config') as $key => $value) {
                $binder->bindConstant($key)
                       ->to($value);
            }
        }

        foreach ($this->otherProperties as $name => $value) {
            $binder->bindConstant($name)
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