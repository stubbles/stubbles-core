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
use net\stubbles\lang\BaseObject;
/**
 * Injection provider which uses a closure to create the instance.
 *
 * @internal
 * @since     2.1.0
 */
class ClosureInjectionProvider extends BaseObject implements InjectionProvider
{
    /**
     * closure to use
     *
     * @type  \Closure
     */
    private $closure;

    /**
     * constructor
     *
     * @param  \Closure  $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        $closure = $this->closure;
        return $closure($name);
    }
}
?>