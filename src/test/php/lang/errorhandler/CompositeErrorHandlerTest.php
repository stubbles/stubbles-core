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
use bovigo\callmap\NewInstance;

use function bovigo\callmap\verify;
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
     * @type  \stubbles\lang\errorhandler\ErrorHandler
     */
    protected $errorHandler1;
    /**
     * a mocked error handler
     *
     * @type  \stubbles\lang\errorhandler\ErrorHandler
     */
    protected $errorHandler2;
    /**
     * a mocked error handler
     *
     * @type  \stubbles\lang\errorhandler\ErrorHandler
     */
    protected $errorHandler3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeErrorHandler = new CompositeErrorHandler();
        $this->errorHandler1 = NewInstance::of(ErrorHandler::class);
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler1);
        $this->errorHandler2 = NewInstance::of(ErrorHandler::class);
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler2);
        $this->errorHandler3 = NewInstance::of(ErrorHandler::class);
        $this->compositeErrorHandler->addErrorHandler($this->errorHandler3);
    }

    /**
     * @test
     */
    public function isResponsibleDoesOnlyCallErrorHandlersUntilResponsibleOneFound()
    {
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(['isResponsible' => true]);
        assertTrue($this->compositeErrorHandler->isResponsible(1, 'foo'));
        verify($this->errorHandler3, 'isResponsible')->wasNeverCalled();
     }

    /**
     * @test
     */
    public function isResponsibleReturnsFalseIfNoHandlerIsResponsible()
    {
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(['isResponsible' => false]);
        $this->errorHandler3->mapCalls(['isResponsible' => false]);
        assertFalse($this->compositeErrorHandler->isResponsible(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsFalseAsSoonAsOneHandlerDeniesSupressability()
    {
        $this->errorHandler1->mapCalls(['isSupressable' => true]);
        $this->errorHandler2->mapCalls(['isSupressable' => false]);
        assertFalse($this->compositeErrorHandler->isSupressable(1, 'foo'));
        verify($this->errorHandler3, 'isSupressable')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function isSupressableReturnsOnlyTrueIfAllHandlerAllowSupressability()
    {
        $this->errorHandler1->mapCalls(['isSupressable' => true]);
        $this->errorHandler2->mapCalls(['isSupressable' => true]);
        $this->errorHandler3->mapCalls(['isSupressable' => true]);
        assertTrue($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function handleSignalsDefaultStrategyIfNoErrorHandlerIsResponsible()
    {
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(['isResponsible' => false]);
        $this->errorHandler3->mapCalls(['isResponsible' => false]);
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
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(
                ['isResponsible' => true, 'isSupressable' => true]
        );
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
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(
                ['isResponsible' => true,
                 'isSupressable' => false,
                 'handle'        => ErrorHandler::STOP_ERROR_HANDLING
                ]
        );
        assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        verify($this->errorHandler3, 'isResponsible')->wasNeverCalled();
        error_reporting($oldLevel);
    }

    /**
     * @test
     */
    public function handleSignalsResultOfResponsibleErrorHandlerIfErrorReportingEnabled()
    {
        $oldLevel = error_reporting(E_ALL);
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(
                ['isResponsible' => true,
                 'isSupressable' => false,
                 'handle'        => ErrorHandler::STOP_ERROR_HANDLING
                ]
        );
        assertEquals(
                ErrorHandler::STOP_ERROR_HANDLING,
                $this->compositeErrorHandler->handle(1, 'foo')
        );
        error_reporting($oldLevel);
    }
}
