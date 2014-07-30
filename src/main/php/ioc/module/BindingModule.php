<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\module;
use stubbles\ioc\Binder;
/**
 * Interface for modules which configure the binder.
 *
 * @api
 */
interface BindingModule
{
    /**
     * configure the binder
     *
     * @param  \stubbles\ioc\Binder  $binder
     */
    public function configure(Binder $binder);
}
