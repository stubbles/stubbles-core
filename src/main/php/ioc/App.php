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
use stubbles\ioc\module\ModeBindingModule;
use stubbles\lang\Mode;
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
        return BindingFactory::createInjector(self::getBindingsForApp($className, $projectPath))
                             ->getInstance($className);
    }

    /**
     * creates list of bindings from given class
     *
     * @param   string  $className    full qualified class name of class to create an instance of
     * @param   string  $projectPath  path to project
     * @return  \stubbles\ioc\module\BindingModule[]
     * @since   1.3.0
     */
    private static function getBindingsForApp($className, $projectPath)
    {
        if (method_exists($className, '__bindings')) {
            return $className::__bindings($projectPath);
        }

        return [];
    }

    /**
     * creates mode binding module
     *
     * @api
     * @param   string         $projectPath  path to project files
     * @param   \stubbles\lang\Mode|callable  $mode         optional  runtime mode
     * @return  \stubbles\ioc\module\ModeBindingModule
     * @since   2.0.0
     */
    protected static function createModeBindingModule($projectPath, $mode = null)
    {
        return new ModeBindingModule($projectPath, $mode);
    }

    /**
     * create a binding module which binds current working directory
     *
     * @api
     * @return  \Closure
     */
    protected static function bindCurrentWorkingDirectory()
    {
        return function(Binder $binder)
        {
            $binder->bindConstant('stubbles.cwd')
                   ->to(getcwd());
        };
    }

    /**
     * create a binding module which binds current hostnames
     *
     * @api
     * @return  \Closure
     */
    protected static function bindHostname()
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
}
