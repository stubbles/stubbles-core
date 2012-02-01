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
use net\stubbles\ioc\module\ArgumentsBindingModule;
use net\stubbles\ioc\module\ModeBindingModule;
use net\stubbles\ioc\module\PropertiesBindingModule;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\Mode;
/**
 * Application base class.
 */
abstract class App extends BaseObject
{
    /**
     * runs the application
     *
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
     * @param   string    $className    full qualified class name of class to create an instance of
     * @param   string    $projectPath  path to project
     * @param   string[]  $argv         list of arguments
     * @return  App
     */
    public static function createInstance($className, $projectPath, array $argv = null)
    {
        return BindingFactory::createInjector(self::getBindingsForApp($className, $projectPath, $argv))
                             ->getInstance($className);
    }

    /**
     * creates list of bindings from given class
     *
     * @param   string    $className    full qualified class name of class to create an instance of
     * @param   string    $projectPath  path to project
     * @param   string[]  $argv         list of arguments
     * @return  BindingModule[]
     * @since   1.3.0
     */
    public static function getBindingsForApp($className, $projectPath, array $argv = null)
    {
        $bindings = array();
        if (method_exists($className, '__bindings')) {
            $bindings = call_user_func_array(array($className, '__bindings'), array($projectPath));
        }

        if (null !== $argv) {
            $bindings[] = new ArgumentsBindingModule($argv);
        }

        return $bindings;
    }

    /**
     * creates mode binding module
     *
     * @param   Mode  $mode  runtime mode
     * @return  ModeBindingModule
     * @since   2.0.0
     */
    protected static function createModeBindingModule(Mode $mode = null)
    {
        return new ModeBindingModule($mode);
    }

    /**
     * creates properties binding module
     *
     * @param   string  $projectPath
     * @return  PropertiesBindingModule
     * @since   2.0.0
     */
    protected static function createPropertiesBindingModule($projectPath)
    {
        return new PropertiesBindingModule($projectPath);
    }
}
?>