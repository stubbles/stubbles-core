<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
use stubbles\ioc\InjectionProvider;
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
     * @param   \ReflectionClass                 $impl      concrete implementation
     * @param   \stubbles\ioc\InjectionProvider  $provider
     * @return  object
     */
    public function getInstance(\ReflectionClass $impl, InjectionProvider $provider);
}
