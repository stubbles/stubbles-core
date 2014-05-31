<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\module;
use stubbles\ioc\Binder;
use stubbles\lang\Mode;
use stubbles\lang\Properties;
/**
 * Binding module to configure the binder with a runtime mode.
 */
class ModeBindingModule implements BindingModule
{
    /**
     * different path types
     *
     * @type  string[]
     */
    private $pathTypes       = ['config',
                                'log'
                               ];
    /**
     * path to config file
     *
     * @type  string
     */
    private $projectPath;
    /**
     * mode instance to bind
     *
     * @type  Mode
     */
    protected $mode;

    /**
     * constructor
     *
     * @param  string  $projectPath
     * @param  Mode    $mode
     */
    public function __construct($projectPath, Mode $mode = null)
    {
        $this->projectPath = $projectPath;
        if (null === $mode) {
            $mode = $this->getFallbackMode();
        }

        $mode->registerErrorHandler($projectPath);
        $mode->registerExceptionHandler($projectPath);
        $this->mode = $mode;
    }

    /**
     * returns fallback mode
     *
     * @return  Mode
     */
    protected function getFallbackMode()
    {
        return \stubbles\lang\DefaultMode::prod();
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
        $binder->bind('stubbles\lang\Mode')
               ->toInstance($this->mode);
        if (file_exists($this->getConfigFilePath())) {
            $binder->bindProperties(Properties::fromFile($this->getConfigFilePath()), $this->mode);
        }

        $binder->bindConstant('stubbles.project.path')
               ->to($this->projectPath);
        foreach ($this->buildPathes($this->projectPath) as $name => $value) {
            $binder->bindConstant($name)
                   ->to($value);
        }
    }

    /**
     * returns path to config file
     *
     * @return  string
     */
    private function getConfigFilePath()
    {
        return $this->projectPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini';
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

        $pathes = [];
        foreach ($this->pathTypes as $pathType) {
            $pathes['stubbles.' . $pathType . '.path'] = $path . $pathType;
        }

        return $pathes;
    }
}
