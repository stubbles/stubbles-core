<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use stubbles\environments\Production;
use stubbles\ioc\Binder;
use stubbles\ioc\module\BindingModule;
use stubbles\lang\Mode;
/**
 * Binding module to configure the binder with a runtime environment.
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
     * current environment we are running in
     *
     * @type  \stubbles\Environment
     */
    protected $environment;

    /**
     * constructor
     *
     * If no environment is passed it will fallback to the default environment.
     *
     * @param   \stubbles\Environment|callable  $environment  optional  current environment
     * @throws  \InvalidArgumentException
     */
    public function __construct($environment = null)
    {
        if (null !== $environment) {
            if (is_callable($environment)) {
                $this->environment = $environment();
            } elseif ($environment instanceof Environment) {
                $this->environment = $environment;
            } else {
                throw new \InvalidArgumentException(
                        'Invalid environment, must either be an instance of '
                        . Environment::class . ' or a callable returning such '
                        . 'an instance'
                );
            }
        } else {
            $this->environment = $this->getFallbackMode();
        }

        self::$initialized = true;
    }

    /**
     * returns fallback environment
     *
     * @return  \stubbles\Environment
     */
    protected function getFallbackMode()
    {
        return new Production();
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
     * @param  string                $projectPath  optional  project base path
     */
    public function configure(Binder $binder, $projectPath = null)
    {
        $this->environment->registerErrorHandler($projectPath);
        $this->environment->registerExceptionHandler($projectPath);
        $binder->setEnvironment($this->environment->name())
                ->bind(Environment::class)->toInstance($this->environment);
        if ($this->environment instanceof Mode) {
            $binder->bind(Mode::class)->toInstance($this->environment);
        }

        if (file_exists($this->propertiesFile($projectPath))) {
            $binder->bindPropertiesFromFile(
                    $this->propertiesFile($projectPath),
                    $this->environment->name()
            );
        }

        $binder->bindConstant('stubbles.project.path')->to($projectPath);
        foreach ($this->buildPathes($projectPath) as $name => $value) {
            $binder->bindConstant($name)->to($value);
        }
    }

    /**
     * returns path to config file
     *
     * @return  string
     */
    private function propertiesFile($projectPath)
    {
        return $projectPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.ini';
    }

    /**
     * appends directory separator if necessary
     *
     * @param   string  $projectPath
     * @return  string
     */
    private function buildPathes($projectPath)
    {
        if (substr($projectPath, -1) !== DIRECTORY_SEPARATOR) {
            $projectPath .= DIRECTORY_SEPARATOR;
        }

        $pathes = [];
        foreach ($this->pathTypes as $pathType) {
            $pathes['stubbles.' . $pathType . '.path'] = $projectPath . $pathType;
        }

        return $pathes;
    }
}
