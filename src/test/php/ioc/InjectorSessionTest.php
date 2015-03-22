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
use stubbles\test\ioc\Mikey;
use stubbles\test\ioc\SessionBindingScope;
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
     * @var  Binder
     */
    protected $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->binder = new Binder();

    }

    /**
     * @test
     * @deprecated  since 5.4.0, will be removed with 6.0.0
     */
    public function storesCreatedInstanceInSession()
    {
        $this->binder->setSessionScope(new SessionBindingScope());
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $injector = $this->binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Person2'));

        $text = $injector->getInstance('stubbles\test\ioc\Person2');
        $this->assertInstanceOf('stubbles\test\ioc\Person2', $text);
        $this->assertInstanceOf('stubbles\test\ioc\Mikey', $text);
        $this->assertSame($text, $injector->getInstance('stubbles\test\ioc\Person2'));
    }

    /**
     * @test
     * @deprecated  since 5.4.0, will be removed with 6.0.0
     */
    public function usesInstanceFromSessionIfAvailable()
    {
        $this->binder->setSessionScope(new SessionBindingScope());
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $injector = $this->binder->getInjector();
        $text     = new Mikey();
        SessionBindingScope::$instances['stubbles\test\ioc\Mikey'] = $text;
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Person2'));
        $this->assertSame($text, $injector->getInstance('stubbles\test\ioc\Person2'));
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function canBindToSessionScopeWithoutSession()
    {
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $this->assertTrue(
                $this->binder->getInjector()
                        ->hasBinding('stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     * @since  5.4.0
     * @expectedException  RuntimeException
     */
    public function requestSessionScopedWithoutSessionThrowsRuntimeException()
    {
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $this->binder->getInjector()->getInstance('stubbles\test\ioc\Person2');
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function requestSessionScopedWithSessionReturnsInstance()
    {
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $injector    = $this->binder->getInjector();
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $mockSession->expects($this->once())
                    ->method('hasValue')
                    ->will($this->returnValue(false));
        $injector->setSession($mockSession);
        $this->assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $injector->getInstance('stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSession()
    {
        $injector    = $this->binder->getInjector();
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $injector->setSession($mockSession, 'stubbles\ioc\binding\Session');
        $this->assertTrue(
                $injector->hasExplicitBinding('stubbles\ioc\binding\Session')
        );
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function setSessionAddsBindingForSessionAsSingleton()
    {
        $injector    = $this->binder->getInjector();
        $mockSession = $this->getMock('stubbles\ioc\binding\Session');
        $injector->setSession($mockSession, 'stubbles\ioc\binding\Session');
        $this->assertSame(
                $mockSession,
                $injector->getInstance('stubbles\ioc\binding\Session')
        );
    }
}
