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
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\binding\BindingScope;
/**
 * Interface for session storages.
 *
 * @since  5.4.0
 */
class SessionBindingScope implements BindingScope
{
    /**
     * session prefix key
     */
    const SESSION_KEY  = 'stubbles.ioc.session.scope#';
    /**
     * session instance to store instances in
     *
     * @type  \stubbles\ioc\binding\Session
     */
    private $session;

    /**
     * sets actual session
     *
     * @param   \stubbles\ioc\binding\Session  $session
     * @return  \stubbles\ioc\binding\SessionBindingScope
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    /**
     * returns the requested instance from the scope
     *
     * @param   \ReflectionClass                 $impl      concrete implementation
     * @param   \stubbles\ioc\InjectionProvider  $provider
     * @return  object
     * @throws  \RuntimeException
     */
    public function getInstance(\ReflectionClass $impl, InjectionProvider $provider)
    {
        if (null === $this->session) {
            throw new \RuntimeException('Can not create session-scoped instance for ' . $impl->getName() . ', no session set in session scope');
        }

        $key = self::SESSION_KEY . $impl->getName();
        if ($this->session->hasValue($key)) {
            return $this->session->value($key);
        }

        $instance = $provider->get();
        $this->session->putValue($key, $instance);
        return $instance;
    }
}
