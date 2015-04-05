<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\errorhandler;
/**
 * Tests for stubbles\lang\errorhandler\CompositeErrorHandler
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class CompositeErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CompositeErrorHandler
     */
    protected $compositeErrorHandler;
    /**
     * a mocked error handler
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler1;
    /**
     * a mocked error handler
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler2;
    /**
     * a mocked error handler
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeErrorHandler = new CompositeErrorHandler();
        $this->mockErrorHandler1     = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler1);
        $this->mockErrorHandler2 = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler2);
        $this->mockErrorHandler3 = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler3);
    }

    /**
     * @test
     */
    public function isResponsibleDoesOnlyCallErrorHandlersUntilResponsibleOneFound()
    {
        $this->mockErrorHandler1->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->method('isResponsible')
                ->will($this->returnValue(true));
        $this->mockErrorHandler3->expects($this->never())
                ->method('isResponsible');
        $this->assertTrue($this->compositeErrorHandler->isResponsible(1, 'foo'));
     }

    /**
     * @test
     */
    public function isResponsibleReturnsFalseIfNoHandlerIsResponsible()
    {
        $this->mockErrorHandler1->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler3->method('isResponsible')
                ->will($this->returnValue(false));
        $this->assertFalse($this->compositeErrorHandler->isResponsible(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsFalseAsSoonAsOneHandlerDeniesSupressability()
    {
        $this->mockErrorHandler1->method('isSupressable')
                ->will($this->returnValue(true));
        $this->mockErrorHandler2->method('isSupressable')
                ->will($this->returnValue(false));
        $this->mockErrorHandler3->expects($this->never())
                ->method('isSupressable');
        $this->assertFalse($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsOnlyTrueIfAllHandlerAllowSupressability()
    {
        $this->mockErrorHandler1->method('isSupressable')
                ->will($this->returnValue(true));
        $this->mockErrorHandler2->method('isSupressable')
                ->will($this->returnValue(true));
        $this->mockErrorHandler3->method('isSupressable')
                ->will($this->returnValue(true));
        $this->assertTrue($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function handleSignalsDefaultStrategyIfNoErrorHandlerIsResponsible()
    {
        $this->mockErrorHandler1->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler3->method('isResponsible')
                ->will($this->returnValue(false));
        $this->assertEquals(
                ErrorHandler::CONTINUE_WITH_PHP_INTERNAL_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
    }

    /**
     * @test
     */
    public function handleSignalsStopIfErrorIsSuppressableAndSuppressedByGlobalErrorReporting()
    {
        $oldLevel = error_reporting(0);
        $this->mockErrorHandler1->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->method('isResponsible')
                ->will($this->returnValue(true));
        $this->mockErrorHandler2->method('isSupressable')
                ->will($this->returnValue(true));
        $this->assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        error_reporting($oldLevel);
    }

    /**
     * @test
     */
    public function handleSignalsResultOfResponsibleErrorHandlerIfErrorReportingDisabled()
    {
        $oldLevel = error_reporting(0);
        $this->mockErrorHandler1->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler2->method('isResponsible')
                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->once())
                ->method('isSupressable')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->once())
                ->method('handle')
                ->will($this->returnValue(ErrorHandler::STOP_ERROR_HANDLING));
        $this->mockErrorHandler3->expects($this->never())
                ->method('isResponsible');
        $this->assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        error_reporting($oldLevel);
    }

    /**
     * @test
     */
    public function handleSignalsResultOfResponsibleErrorHandlerIfErrorReportingEnabled()
    {
        $oldLevel = error_reporting(E_ALL);
        $this->mockErrorHandler1->method('isResponsible')
                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->once())
                ->method('isResponsible')
                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->once())
                ->method('handle')
                ->will($this->returnValue(ErrorHandler::STOP_ERROR_HANDLING));
        $this->assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        error_reporting($oldLevel);
    }
}
