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
    protected $errorHandler1;
    /**
     * a mocked error handler
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $errorHandler2;
    /**
     * a mocked error handler
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $errorHandler3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeErrorHandler = new CompositeErrorHandler();
        $this->errorHandler1 = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler1);
        $this->errorHandler2 = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler2);
        $this->errorHandler3 = $this->getMock('stubbles\lang\errorhandler\ErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler3);
    }

    /**
     * @test
     */
    public function isResponsibleDoesOnlyCallErrorHandlersUntilResponsibleOneFound()
    {
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->method('isResponsible')->will(returnValue(true));
        $this->errorHandler3->expects(never())->method('isResponsible');
        assertTrue($this->compositeErrorHandler->isResponsible(1, 'foo'));
     }

    /**
     * @test
     */
    public function isResponsibleReturnsFalseIfNoHandlerIsResponsible()
    {
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->method('isResponsible')->will(returnValue(false));
        $this->errorHandler3->method('isResponsible')->will(returnValue(false));
        assertFalse($this->compositeErrorHandler->isResponsible(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsFalseAsSoonAsOneHandlerDeniesSupressability()
    {
        $this->errorHandler1->method('isSupressable')->will(returnValue(true));
        $this->errorHandler2->method('isSupressable')->will(returnValue(false));
        $this->errorHandler3->expects(never())->method('isSupressable');
        assertFalse($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsOnlyTrueIfAllHandlerAllowSupressability()
    {
        $this->errorHandler1->method('isSupressable')->will(returnValue(true));
        $this->errorHandler2->method('isSupressable')->will(returnValue(true));
        $this->errorHandler3->method('isSupressable')->will(returnValue(true));
        assertTrue($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function handleSignalsDefaultStrategyIfNoErrorHandlerIsResponsible()
    {
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->method('isResponsible')->will(returnValue(false));
        $this->errorHandler3->method('isResponsible')->will(returnValue(false));
        assertEquals(
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
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->method('isResponsible')->will(returnValue(true));
        $this->errorHandler2->method('isSupressable')->will(returnValue(true));
        assertEquals(
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
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->method('isResponsible')->will(returnValue(true));
        $this->errorHandler2->expects(once())
                ->method('isSupressable')
                ->will(returnValue(false));
        $this->errorHandler2->expects(once())
                ->method('handle')
                ->will(returnValue(ErrorHandler::STOP_ERROR_HANDLING));
        $this->errorHandler3->expects(never())->method('isResponsible');
        assertEquals(
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
        $this->errorHandler1->method('isResponsible')->will(returnValue(false));
        $this->errorHandler2->expects(once())
                ->method('isResponsible')
                ->will(returnValue(true));
        $this->errorHandler2->expects(once())
                ->method('handle')
                ->will(returnValue(ErrorHandler::STOP_ERROR_HANDLING));
        assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        error_reporting($oldLevel);
    }
}
