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
/**
 * Binding module to configure the binder with a runtime mode.
 */
class Runtime implements BindingModule
{
    /**
     * marker whether runtime was already initialized
     *
     * @type  bool
     */
    private static $initialized = false;

    /**
     * checks whether runtime was already bound
     *
     * @internal
     * @return  bool
     */
    public static function initialized()
    {
        return self::$initialized;
    }

    /**
     * resets initialzed status
     *
     * @internal
     */
    public static function reset()
    {
        self::$initialized = false;
    }

    /**
     * different path types
     *
     * @type  string[]
     */
    private $pathTypes   = ['config', 'log'];
    /**
     * path to config file
     *
     * @type  string
     */
    protected $projectPath;
    /**
     * mode instance to bind
     *
     * @type  \stubbles\lang\Mode
     */
    protected $mode;

    /**
     * constructor
     *
     * @param   string                        $projectPath  path to project files
     * @param   \stubbles\lang\Mode|callable  $mode         optional  runtime mode
     * @throws  \InvalidArgumentException
     */
    public function __construct($projectPath, $mode = null)
    {
        $this->projectPath = $projectPath;
        if (null !== $mode) {
            if (is_callable($mode)) {
                $this->mode = $mode();
            } elseif ($mode instanceof Mode) {
                $this->mode = $mode;
            } else {
                throw new \InvalidArgumentException(
                        'Invalid mode, must either be an instance of'
                        . ' stubbles\lang\Mode or a callable returning such an'
                        . ' instance'
                );
            }
        } else {
            $this->mode = $this->getFallbackMode();
        }

        $this->mode->registerErrorHandler($projectPath);
        $this->mode->registerExceptionHandler($projectPath);
        self::$initialized = true;
    }

    /**
     * returns fallback mode
     *
     * @return  \stubbles\lang\Mode
     */
    protected function getFallbackMode()
    {
        return \stubbles\lang\DefaultMode::prod();
    }

    /**
     * adds a path type to be bound
     *
     * The path type will lead to a path available via injection. The constant
     * name of this path will be <i>stubbles.$pathtype.path</i> and it's
     * value will be $projectPath/$pathtype.
     *
     * @api
     * @param   string  $pathType
     * @return  \stubbles\ioc\module\Runtime
     */
    public function addPathType($pathType)
    {
        $this->pathTypes[] = $pathType;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  \stubbles\ioc\Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $binder->bindMode($this->mode);
        if (file_exists($this->propertiesFile())) {
            $binder->bindPropertiesFromFile($this->propertiesFile(), $this->mode);
        }

        $binder->bindConstant('stubbles.project.path')->to($this->projectPath);
        foreach ($this->buildPathes($this->projectPath) as $name => $value) {
            $binder->bindConstant($name)->to($value);
        }
    }

    /**
     * returns path to config file
     *
     * @return  string
     */
    private function propertiesFile()
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
