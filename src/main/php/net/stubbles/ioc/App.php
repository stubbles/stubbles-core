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
     * @return  App
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
     * @return  App
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
     * @return  BindingModule[]
     * @since   1.3.0
     */
    private static function getBindingsForApp($className, $projectPath)
    {
        if (method_exists($className, '__bindings')) {
            return $className::__bindings($projectPath);
        }

        return array();
    }

    /**
     * enable persistent annotation cache by telling where to store cache data
     *
     * @param  string  $cacheFile
     * @since  2.2.0
     */
    protected static function persistAnnotationCache($cacheFile)
    {
        \net\stubbles\lang\reflect\annotation\AnnotationCache::start($cacheFile);
    }

    /**
     * creates mode binding module
     *
     * @api
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
     * @api
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