<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\ioc\module\Runtime;
/**
 * Application base class.
 */
abstract class App
{
    /**
     * runs the application
     *
     * @api
     * @since  2.0.0
     */
    public abstract function run();

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @api
     * @param   string  $projectPath  path to project
     * @return  \stubbles\ioc\App
     */
    public static function create($projectPath)
    {
        return self::createInstance(get_called_class(), $projectPath);
    }

    /**
     * project path, set when instance is created
     *
     * @type  string
     */
    private static $projectPath;

    /**
     * returns current project path
     *
     * Should only be used during app initialization, but never at runtime.
     *
     * @since   5.0.0
     * @return  string
     */
    protected static function projectPath()
    {
        return self::$projectPath;
    }

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @api
     * @param   string  $className    full qualified class name of class to create an instance of
     * @param   string  $projectPath  path to project
     * @return  \stubbles\ioc\App
     */
    public static function createInstance($className, $projectPath)
    {
        Runtime::reset();
        self::$projectPath = $projectPath;
        return BindingFactory::createInjector(
                        static::getBindingsForApp($className)
               )
               ->getInstance($className);
    }

    /**
     * creates list of bindings from given class
     *
     * @internal  must not be used by applications
     * @param   string  $className    full qualified class name of class to create an instance of
     * @param   string  $projectPath  path to project
     * @return  \stubbles\ioc\module\BindingModule[]
     * @since   1.3.0
     */
    protected static function getBindingsForApp($className)
    {
        $bindings = method_exists($className, '__bindings') ? $className::__bindings() : [];
        if (!Runtime::initialized()) {
            $bindings[] = static::runtime();
        }

        return $bindings;
    }

    /**
     * creates mode binding module
     *
     * @api
     * @param   \stubbles\lang\Mode|callable  $mode  optional  runtime mode
     * @return  \stubbles\ioc\module\Runtime
     * @since   2.0.0
     */
    protected static function runtime($mode = null)
    {
        return new Runtime(self::projectPath(), $mode);
    }

    /**
     * create a binding module which binds current working directory
     *
     * @api
     * @return  \Closure
     */
    protected static function currentWorkingDirectory()
    {
        return function(Binder $binder)
        {
            $binder->bindConstant('stubbles.cwd')
                   ->to(getcwd());
        };
    }

    /**
     * create a binding module which binds current working directory
     *
     * @api
     * @return  \Closure
     * @deprecated  since 5.0.0, use currentWorkingDirectory() instead, will be removed with 6.0.0
     */
    protected static function bindCurrentWorkingDirectory()
    {
        return self::currentWorkingDirectory();
    }

    /**
     * create a binding module which binds current hostnames
     *
     * @api
     * @return  \Closure
     */
    protected static function hostname()
    {
        return function(Binder $binder)
        {
            if (DIRECTORY_SEPARATOR === '\\') {
                $fq = php_uname('n');
                if (isset($_SERVER['USERDNSDOMAIN'])) {
                    $fq .= '.' . $_SERVER['USERDNSDOMAIN'];
                }
            } else {
                $fq = exec('hostname -f');
            }

            $binder->bindConstant('stubbles.hostname.nq')
                   ->to(php_uname('n'));
            $binder->bindConstant('stubbles.hostname.fq')
                   ->to($fq);
        };
    }

    /**
     * create a binding module which binds current hostnames
     *
     * @api
     * @return  \Closure
     * @deprecated  since 5.0.0, use hostname() instead, will be removed with 6.0.0
     */
    protected static function bindHostname()
    {
        return self::hostname();
    }
}
