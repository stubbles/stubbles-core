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
use stubbles\ioc\module\BindingModule;
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for starting the application by configuring the IoC container.
 *
 * @since     2.0.0
 * @internal
 */
class BindingFactory
{
    /**
     * configures the injector using the given binding modules
     *
     * An arbitrary list of modules can be provided. For example, to provide
     * three different modules:
     * <code>
     * BindingFactory::createInjector($module1, $module2, $module3);
     * </code>
     *
     * @return  \stubbles\ioc\Injector
     */
    public static function createInjector()
    {
        return self::createBinder(self::extractArgs(func_get_args()))->getInjector();
    }

    /**
     * configures the application using the given binding modules and returns
     * binder so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   \stubbles\ioc\module\BindingModule[]  $bindingModules
     * @return  \stubbles\ioc\Binder
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     * @since   1.3.0
     */
    public static function createBinder(array $bindingModules)
    {
        $binder = new Binder();
        foreach ($bindingModules as $bindingModule) {
            if (is_string($bindingModule)) {
                $bindingModule = new $bindingModule();
            }

            if ($bindingModule instanceof BindingModule) {
                $bindingModule->configure($binder);
            } elseif ($bindingModule instanceof \Closure) {
                $bindingModule($binder);
            } else {
                throw new IllegalArgumentException('Given module class ' . get_class($bindingModule) . ' is not an instance of stubbles\ioc\module\BindingModule');
            }
        }

        return $binder;
    }

    /**
     * extracts arguments
     *
     * If arguments has only one value and this is an array this will be returned,
     * else all arguments will be returned.
     *
     * @param   array  $args
     * @return  \stubbles\ioc\module\BindingModule[]
     */
    protected static function extractArgs(array $args)
    {
        if (count($args) === 1 && is_array($args[0])) {
            return $args[0];
        }

        return $args;
    }
}
