<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test;
use stubbles\ioc\Binder;
use stubbles\ioc\module\BindingModule;
/**
 * Helper class for ioc tests.
 */
class AppTestBindingModuleTwo implements BindingModule
{
    /**
     * configure the binder
     *
     * @param  \stubbles\ioc\Binder  $binder
     * @param  string                $projectPath  optional  project base path
     */
    public function configure(Binder $binder, $projectPath = null)
    {
        $binder->bind('bar')->to('stdClass');
    }
}
