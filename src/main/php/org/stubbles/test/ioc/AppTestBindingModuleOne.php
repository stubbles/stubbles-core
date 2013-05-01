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
use net\stubbles\ioc\Binder;
use net\stubbles\ioc\module\BindingModule;
/**
 * Helper class for ioc tests.
 */
class AppTestBindingModuleOne implements BindingModule
{
    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $binder->bind('foo')->to('\\stdClass');
    }
}
?>