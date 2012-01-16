<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\module;
use net\stubbles\ioc\Binder;
use net\stubbles\lang\BaseObject;
/**
 * Binding module to configure the binder with arguments.
 */
class ArgumentsBindingModule extends BaseObject implements BindingModule
{
    /**
     * list of arguments
     *
     * @type  string[]
     */
    protected $argv;

    /**
     * constructor
     *
     * @param  string[]  $argv
     */
    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        foreach ($this->argv as $position => $value) {
            $binder->bindConstant()
                   ->named('argv.' . $position)
                   ->to($value);
        }
    }
}
?>