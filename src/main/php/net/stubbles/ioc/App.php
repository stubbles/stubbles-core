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
use net\stubbles\ioc\module\BindingModule;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for starting the application by configuring the IoC container.
 */
class App extends BaseObject
{
    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @return  Injector
     */
    public static function createInjector()
    {
        return self::createInjectorWithBindings(self::extractArgs(func_get_args()));
    }

    /**
     * extracts arguments
     *
     * If arguments has only one value and this is an array this will be returned,
     * else all arguments will be returned.
     *
     * @param   array  $args
     * @return  BindingModule[]
     */
    protected static function extractArgs(array $args)
    {
        if (count($args) === 1 && is_array($args[0])) {
            return $args[0];
        }

        return $args;
    }

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
     * @return  object
     */
    public static function createInstance($className, $projectPath, array $argv = null)
    {
        return self::createInjectorWithBindings(self::getBindingsForClass($className, $projectPath, $argv))
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
    public static function getBindingsForClass($className, $projectPath, array $argv = null)
    {
        $bindings = array();
        if (method_exists($className, '__bindings')) {
            $bindings = call_user_func_array(array($className, '__bindings'), array($projectPath));
        }

        if (null !== $argv) {
            $bindings[] = new module\ArgumentsBindingModule($argv);
        }

        return $bindings;
    }

    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   BindingModule[]  $bindingModules
     * @return  Injector
     */
    public static function createInjectorWithBindings(array $bindingModules)
    {
        return self::createBinderWithBindings($bindingModules)->getInjector();
    }

    /**
     * configures the application using the given binding modules and returns
     * binder so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   BindingModule[]  $bindingModules
     * @return  Binder
     * @throws  IllegalArgumentException
     * @since   1.3.0
     */
    public static function createBinderWithBindings(array $bindingModules)
    {
        $binder = new Binder();
        foreach ($bindingModules as $bindingModule) {
            if (is_string($bindingModule)) {
                $bindingModule = new $bindingModule();
            }

            if (!($bindingModule instanceof BindingModule)) {
                throw new IllegalArgumentException('Given module class ' . get_class($bindingModule) . ' is not an instance of net\\stubbles\\ioc\\module\\BindingModule');
            }

            /* @type  $bindingModule  BindingModule */
            $bindingModule->configure($binder);
        }

        $binder->bind('net\\stubbles\\ioc\\Injector')
               ->toInstance($binder->getInjector());
        return $binder;
    }
}
?>