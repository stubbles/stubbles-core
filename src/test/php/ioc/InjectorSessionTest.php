<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use bovigo\callmap\NewInstance;
use stubbles\ioc\binding\Session;
use stubbles\test\ioc\Mikey;
use stubbles\test\ioc\Person2;
/**
 * Test for stubbles\ioc\Injector with the session scope.
 *
 * @group  ioc
 */
class InjectorSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * binder instance to be used in tests
     *
     * @type  \stubbles\ioc\Injector
     */
    private $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $binder = new Binder();
        $binder->bind(Person2::class)
                ->to(Mikey::class)
                ->inSession();
        $this->injector = $binder->getInjector();

    }
    /**
     * @test
     * @since  5.4.0
     */
    public function canBindToSessionScopeWithoutSession()
    {
        assertTrue(
                $this->injector->hasBinding(Person2::class)
        );
    }

    /**
     * @test
     * @since  5.4.0
     * @expectedException  RuntimeException
     */
    public function requestSessionScopedWithoutSessionThrowsRuntimeException()
    {
        $this->injector->getInstance(Person2::class);
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function requestSessionScopedWithSessionReturnsInstance()
    {
        $session = NewInstance::of(Session::class)
                ->mapCalls(['hasValue' => false]);
        assertInstanceOf(
                Mikey::class,
                $this->injector->setSession($session)
                        ->getInstance(Person2::class)
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSession()
    {
        assertTrue(
                $this->injector->setSession(
                        NewInstance::of(Session::class),
                        Session::class
                )->hasExplicitBinding(Session::class)
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSessionAsSingleton()
    {
        $session = NewInstance::of(Session::class);
        assertSame(
                $session,
                $this->injector->setSession(
                        $session,
                        Session::class
                )->getInstance(Session::class)
        );
    }
}
