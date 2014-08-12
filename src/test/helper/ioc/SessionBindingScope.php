<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\binding\BindingScope;
use stubbles\lang\reflect\BaseReflectionClass;
/**
 * Session binding scope for the purpose of this test.
 */
class SessionBindingScope implements BindingScope
{
    /**
     * simulate session, sufficient for purpose of this test
     *
     * @type  array
     */
    public static $instances = array();

    /**
     * returns the requested instance from the scope
     *
     * @param   BaseReflectionClass  $impl      concrete implementation
     * @param   InjectionProvider    $provider
     * @return  mixed
     */
    public function getInstance(BaseReflectionClass $impl, InjectionProvider $provider)
    {
        $key = $impl->getName();
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        self::$instances[$key] = $provider->get();
        return self::$instances[$key];
    }
}
