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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use stubbles\ioc\InjectionProvider;
use stubbles\lang;
/**
 * Tests for stubbles\ioc\binding\SessionBindingScope.
 *
 * @since  5.4.0
 * @group  ioc
 * @group  ioc_binding
 */
class SessionBindingScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\ioc\binding\SessionBindingScope
     */
    private $sessionScope;
    /**
     * mocked session id
     *
     * @type  \stubbles\ioc\binding\Session
     */
    private $session;
    /**
     * mocked injection provider
     *
     * @type  \stubbles\ioc\InjectionProvider
     */
    private $provider;

    /**
     * set up test enviroment
     */
    public function setUp()
    {
        $this->session      = NewInstance::of(Session::class);
        $this->sessionScope = new SessionBindingScope();
        $this->provider     = NewInstance::of(InjectionProvider::class);
    }

    /**
     * @test
     */
    public function returnsInstanceFromSessionIfPresent()
    {
        $instance = new \stdClass();
        $this->session->mapCalls(['hasValue' => true, 'value' => $instance]);
        assertSame(
                $instance,
                $this->sessionScope->setSession($this->session)
                        ->getInstance(
                                lang\reflect('\stdClass'),
                                $this->provider
                )
        );
        callmap\verify($this->provider, 'get')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function createsInstanceIfNotPresent()
    {
        $instance = new \stdClass();
        $this->session->mapCalls(['hasValue' => false]);
        $this->provider->mapCalls(['get' => $instance]);
        assertSame(
                $instance,
                $this->sessionScope->setSession($this->session)
                        ->getInstance(
                                lang\reflect('\stdClass'),
                                $this->provider
                )
        );
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function throwsRuntimeExceptionWhenCreatedWithoutSession()
    {
        $this->sessionScope->getInstance(
                lang\reflect('\stdClass'),
                $this->provider
        );
    }
}
