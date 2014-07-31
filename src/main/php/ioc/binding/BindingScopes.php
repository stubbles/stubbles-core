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
use stubbles\lang\exception\RuntimeException;
/**
 * All built-in scopes.
 *
 * @internal
 */
class BindingScopes
{
    /**
     * scope for singleton objects
     *
     * @type  \stubbles\ioc\binding\BindingScope
     */
    protected $singletonScope;
    /**
     * scope for session resources
     *
     * @type  \stubbles\ioc\binding\BindingScope
     */
    protected $sessionScope;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\binding\BindingScope  $singletonScope
     * @param  \stubbles\ioc\binding\BindingScope  $sessionScope
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
     * @return  \stubbles\ioc\binding\BindingScope
     * @since   1.5.0
     */
    public function getSingletonScope()
    {
        return $this->singletonScope;
    }

    /**
     * sets session binding scope
     *
     * @param   \stubbles\ioc\binding\BindingScope  $sessionScope
     * @return  \stubbles\ioc\binding\BindingScopes
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
     * @return  \stubbles\ioc\binding\BindingScope
     * @throws  \stubbles\lang\exception\RuntimeException
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
