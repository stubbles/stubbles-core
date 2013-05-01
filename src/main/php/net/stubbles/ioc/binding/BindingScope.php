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
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Interface for all scopes.
 *
 * @api
 */
interface BindingScope
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