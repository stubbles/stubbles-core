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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
/**
 * All built-in scopes.
 *
 * @internal
 */
class BindingScopes extends BaseObject
{
    /**
     * scope for singleton objects
     *
     * @type  BindingScope
     */
    protected $singletonScope;
    /**
     * scope for session resources
     *
     * @type  BindingScope
     */
    protected $sessionScope;

    /**
     * constructor
     *
     * @param  BindingScope  $singletonScope
     * @param  BindingScope  $sessionScope
     * @since  1.5.0
     */
    public function  __construct(BindingScope $singletonScope = null, BindingScope $sessionScope = null)
    {
        $this->singletonScope = ((null === $singletonScope) ? (new SingletonBindingScope()) : ($singletonScope));
        if (null !== $sessionScope) {
            $this->sessionScope = $sessionScope;
        }
    }

    /**
     * returns scope for singleton objects
     *
     * @return  BindingScope
     * @since   1.5.0
     */
    public function getSingletonScope()
    {
        return $this->singletonScope;
    }

    /**
     * sets session binding scope
     *
     * @param   BindingScope  $sessionScope
     * @return  BindingScopes
     */
    public function setSessionScope(BindingScope $sessionScope)
    {
        $this->sessionScope = $sessionScope;
        return $this;
    }

    /**
     * returns scope for session resources
     *
     * If no session scope is known a RuntimeException will be thrown.
     *
     * @return  BindingScope
     * @throws  RuntimeException
     * @since   1.5.0
     */
    public function getSessionScope()
    {
        if (null === $this->sessionScope) {
            throw new RuntimeException('No explicit session binding scope set.');
        }

        return $this->sessionScope;
    }
}
?>