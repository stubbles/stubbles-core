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
use net\stubbles\lang\Object;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Interface for all scopes
 */
interface BindingScope extends Object
{
    /**
     * returns the requested instance from the scope
     *
     * @param   BaseReflectionClass  $impl      concrete implementation
     * @param   InjectionProvider    $provider
     * @return  Object
     */
    public function getInstance(BaseReflectionClass $impl, InjectionProvider $provider);
}
?>