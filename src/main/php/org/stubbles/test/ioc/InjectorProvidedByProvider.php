<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace org\stubbles\test\ioc;
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
/**
 * Provider class for the test.
 */
class InjectorProvidedByProvider extends BaseObject implements InjectionProvider
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
?>