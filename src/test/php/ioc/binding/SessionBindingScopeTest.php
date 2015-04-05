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
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSession;
    /**
     * mocked injection provider
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInjectionProvider;

    /**
     * set up test enviroment
     */
    public function setUp()
    {
        $this->mockSession           = $this->getMock('stubbles\ioc\binding\Session');
        $this->sessionScope          = new SessionBindingScope();
        $this->mockInjectionProvider = $this->getMock('stubbles\ioc\InjectionProvider');
    }

    /**
     * @test
     */
    public function returnsInstanceFromSessionIfPresent()
    {
        $instance = new \stdClass();
        $this->mockSession->method('hasValue')->will(returnValue(true));
        $this->mockSession->method('value')->will(returnValue($instance));
        $this->mockInjectionProvider->expects(never())->method('get');
        $this->assertSame(
                $instance,
                $this->sessionScope->setSession($this->mockSession)
                        ->getInstance(
                                lang\reflect('\stdClass'),
                                $this->mockInjectionProvider
                )
        );
    }

    /**
     * @test
     */
    public function createsInstanceIfNotPresent()
    {
        $instance = new \stdClass();
        $this->mockSession->method('hasValue')->will(returnValue(false));
        $this->mockSession->expects(never())->method('value');
        $this->mockInjectionProvider->expects(once())
                ->method('get')
                ->will(returnValue($instance));
        $this->assertSame(
                $instance,
                $this->sessionScope->setSession($this->mockSession)
                        ->getInstance(
                                lang\reflect('\stdClass'),
                                $this->mockInjectionProvider
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
                $this->mockInjectionProvider
        );
    }
}
