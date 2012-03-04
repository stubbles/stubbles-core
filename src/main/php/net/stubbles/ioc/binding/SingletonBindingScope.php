<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\binding;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Ensures that an object instance is only created once.
 *
 * @internal
 */
class SingletonBindingScope extends BaseObject implements BindingScope
{
    /**
     * instances in this scope
     *
     * @type  object[]
     */
    protected $instances = array();

    /**
     * returns the requested instance from the scope
     *
     * @param   BaseReflectionClass  $impl      concrete implementation
     * @param   InjectionProvider    $provider
     * @return  object
     */
    public function getInstance(BaseReflectionClass $impl, InjectionProvider $provider)
    {
        $key = $impl->getName();
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $provider->get();
        }

        return $this->instances[$key];
    }
}
?>