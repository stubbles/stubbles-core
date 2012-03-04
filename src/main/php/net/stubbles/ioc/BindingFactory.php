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
 *
 * @since  2.0.0
 */
class BindingFactory extends BaseObject
{
    /**
     * configures the injectpr using the given binding modules
     *
     * @param   array|string...
     * @return  Injector
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
     * @param   BindingModule[]  $bindingModules
     * @return  Binder
     * @throws  IllegalArgumentException
     * @since   1.3.0
     */
    public static function createBinder(array $bindingModules)
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

        return $binder;
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
}
?>