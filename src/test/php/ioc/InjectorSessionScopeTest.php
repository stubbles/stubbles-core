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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isTrue;
/**
 * Test for stubbles\ioc\Injector with the session scope.
 *
 * @group  ioc
 */
class InjectorSessionScopeTest extends \PHPUnit_Framework_TestCase
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
        assert($this->injector->hasBinding(Person2::class), isTrue());
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
        assert(
                $this->injector->setSession($session)
                        ->getInstance(Person2::class),
                isInstanceOf(Mikey::class)
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSession()
    {
        assert(
                $this->injector->setSession(
                        NewInstance::of(Session::class),
                        Session::class
                )->hasExplicitBinding(Session::class),
                isTrue()
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSessionAsSingleton()
    {
        $session = NewInstance::of(Session::class);
        assert(
                $this->injector->setSession(
                        $session,
                        Session::class
                )->getInstance(Session::class),
                isSameAs($session)
        );
    }
}
