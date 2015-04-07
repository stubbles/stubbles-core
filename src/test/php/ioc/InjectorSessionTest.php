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
use stubbles\lang\reflect\NewInstance;
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
        $binder->bind('stubbles\test\ioc\Person2')
                ->to('stubbles\test\ioc\Mikey')
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
                $this->injector->hasBinding('stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     * @since  5.4.0
     * @expectedException  RuntimeException
     */
    public function requestSessionScopedWithoutSessionThrowsRuntimeException()
    {
        $this->injector->getInstance('stubbles\test\ioc\Person2');
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function requestSessionScopedWithSessionReturnsInstance()
    {
        $session = NewInstance::of(
                'stubbles\ioc\binding\Session',
                ['hasValue' => false]
        );
        assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $this->injector->setSession($session)
                        ->getInstance('stubbles\test\ioc\Person2')
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
                        NewInstance::of('stubbles\ioc\binding\Session'),
                        'stubbles\ioc\binding\Session'
                )->hasExplicitBinding('stubbles\ioc\binding\Session')
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSessionAsSingleton()
    {
        $session = NewInstance::of('stubbles\ioc\binding\Session');
        assertSame(
                $session,
                $this->injector->setSession(
                        $session,
                        'stubbles\ioc\binding\Session'
                )->getInstance('stubbles\ioc\binding\Session')
        );
    }
}
