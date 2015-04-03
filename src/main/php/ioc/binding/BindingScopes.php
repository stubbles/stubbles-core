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
    private $singletonScope;
    /**
     * scope for session resources
     *
     * @type  \stubbles\ioc\binding\BindingScope
     */
    private $sessionScope;

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
        $this->sessionScope   = ((null === $sessionScope) ? (new SessionBindingScope()) : ($sessionScope));
    }

    /**
     * returns scope for singleton objects
     *
     * @return  \stubbles\ioc\binding\BindingScope
     * @since   1.5.0
     */
    public function singleton()
    {
        return $this->singletonScope;
    }

    /**
     * sets the session for the session scope in case it is the built-in implementation
     *
     * @param   \stubbles\ioc\binding\Session  $session
     * @return  \stubbles\ioc\binding\BindingScopes
     * @throws  \RuntimeException  in case the session scope has been replaced with another implementation
     * @since   5.4.0
     */
    public function setSession(Session $session)
    {
        if ($this->sessionScope instanceof SessionBindingScope) {
            $this->sessionScope->setSession($session);
            return $this;
        }

        throw new \RuntimeException('Can not set session for session scope implementation ' . get_class($this->sessionScope));
    }

    /**
     * returns scope for session resources
     *
     * @return  \stubbles\ioc\binding\BindingScope
     * @since   1.5.0
     */
    public function session()
    {
        return $this->sessionScope;
    }
}
