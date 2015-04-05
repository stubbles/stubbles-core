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
        $this->assertTrue(
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
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $mockSession->method('hasValue')->will(returnValue(false));
        $this->assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $this->injector->setSession($mockSession)
                        ->getInstance('stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSession()
    {
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $this->assertTrue(
                $this->injector->setSession(
                        $mockSession,
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
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $this->assertSame(
                $mockSession,
                $this->injector->setSession(
                        $mockSession,
                        'stubbles\ioc\binding\Session'
                        )->getInstance('stubbles\ioc\binding\Session')
        );
    }
}
