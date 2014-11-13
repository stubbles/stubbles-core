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
/**
 * Provider class for the test.
 */
class InjectorProvidedByProvider implements InjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new Schst();
    }
}
