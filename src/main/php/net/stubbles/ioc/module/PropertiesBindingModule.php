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
    protected $pathTypes   = array('cache',
                                   'config',
                                   'data',
                                   'docroot',
                                   'log',
                                   'pages'
                             );
    /**
     * path to project files
     *
     * @type  string
     */
    protected $projectPath;

    /**
     * constructor
     *
     * @param  string    $projectPath  path to project
     * @param  string[]  $pathTypes    the different possible path types
     */
    public function __construct($projectPath, array $pathTypes = null)
    {
        if (null !== $pathTypes) {
            $this->pathTypes = array_merge($this->pathTypes, $pathTypes);
        }

        $this->projectPath = $projectPath;

    }

    /**
     * static constructor
     *
     * @param   string    $projectPath  path to project
     * @param   string[]  $pathTypes    the different possible path types
     * @return  PropertiesBindingModule
     */
    public static function create($projectPath, array $pathTypes = null)
    {
        return new self($projectPath, $pathTypes);
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $configPath = null;
        foreach ($this->createPathes($this->projectPath) as $name => $value) {
            $binder->bindConstant()
                   ->named($name)
                   ->to($value);
            if ('net.stubbles.config.path' === $name) {
                $configPath = $value;
            }
        }

        foreach ($this->getProperties($configPath) as $key => $value) {
            $binder->bindConstant()
                   ->named($key)
                   ->to($value);
        }
    }

    /**
     * appends directory separator if necessary
     *
     * @param   string  $path
     * @param   string  $suffix
     * @return  string
     */
    protected function createPathes($path, $suffix = null)
    {
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $pathes = array();
        foreach ($this->pathTypes as $pathType) {
            $pathes['net.stubbles.' . $pathType . '.path' . $suffix] = $path . $pathType;
        }

        return $pathes;
    }

    /**
     * emulate real path to be able to mock this in unit tests
     *
     * @param   string  $path
     * @return  string|bool
     */
    protected function realpath($path)
    {
        return realpath($path);
    }

    /**
     * returns list of properties
     *
     * @param   string  $configPath
     * @return  scalar[]
     */
    protected function getProperties($configPath)
    {
        if (null == $configPath || !file_exists($configPath . DIRECTORY_SEPARATOR . 'config.ini')) {
            return array();
        }

        return parse_ini_file($configPath . DIRECTORY_SEPARATOR . 'config.ini');
    }
}
?>