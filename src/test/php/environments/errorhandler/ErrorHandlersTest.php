<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments\errorhandler;
use bovigo\callmap\NewInstance;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Tests for stubbles\environments\errorhandler\ErrorHandlers
 *
 * @group  environments
 * @group  environments_errorhandler
 */
class ErrorHandlersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ErrorHandlers
     */
    protected $errorHandlers;
    /**
     * a mocked error handler
     *
     * @type  \stubbles\environments\errorhandler\ErrorHandler
     */
    protected $errorHandler1;
    /**
     * a mocked error handler
     *
     * @type  \stubbles\environments\errorhandler\ErrorHandler
     */
    protected $errorHandler2;
    /**
     * a mocked error handler
     *
     * @type  \stubbles\environments\errorhandler\ErrorHandler
     */
    protected $errorHandler3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->errorHandlers = new ErrorHandlers();
        $this->errorHandler1 = NewInstance::of(ErrorHandler::class);
        $this->errorHandlers->addErrorHandler($this->errorHandler1);
        $this->errorHandler2 = NewInstance::of(ErrorHandler::class);
        $this->errorHandlers->addErrorHandler($this->errorHandler2);
        $this->errorHandler3 = NewInstance::of(ErrorHandler::class);
        $this->errorHandlers->addErrorHandler($this->errorHandler3);
    }

    /**
     * @test
     */
    public function isResponsibleDoesOnlyCallErrorHandlersUntilResponsibleOneFound()
    {
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(['isResponsible' => true]);
        assertTrue($this->errorHandlers->isResponsible(1, 'foo'));
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
        assertFalse($this->errorHandlers->isResponsible(1, 'foo'));
    }

    /**
     * @test
     */
    public function isSupressableReturnsFalseAsSoonAsOneHandlerDeniesSupressability()
    {
        $this->errorHandler1->mapCalls(['isSupressable' => true]);
        $this->errorHandler2->mapCalls(['isSupressable' => false]);
        assertFalse($this->errorHandlers->isSupressable(1, 'foo'));
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
        assertTrue($this->errorHandlers->isSupressable(1, 'foo'));
    }

    /**
     * @test
     */
    public function handleSignalsDefaultStrategyIfNoErrorHandlerIsResponsible()
    {
        $this->errorHandler1->mapCalls(['isResponsible' => false]);
        $this->errorHandler2->mapCalls(['isResponsible' => false]);
        $this->errorHandler3->mapCalls(['isResponsible' => false]);
        assert(
                $this->errorHandlers->handle(1, 'foo'),
                equals(ErrorHandler::CONTINUE_WITH_PHP_INTERNAL_HANDLING)
        );
    }

    /**
     * @test
     */
    public function handleSignalsStopIfErrorIsSuppressableAndSuppressedByGlobalErrorReporting()
    {
        $oldLevel = error_reporting(0);
        try {
            $this->errorHandler1->mapCalls(['isResponsible' => false]);
            $this->errorHandler2->mapCalls(
                    ['isResponsible' => true, 'isSupressable' => true]
            );
            assert(
                    $this->errorHandlers->handle(1, 'foo'),
                    equals(ErrorHandler::STOP_ERROR_HANDLING)
            );
        } finally {
            error_reporting($oldLevel);
        }
    }

    /**
     * @test
     */
    public function handleSignalsResultOfResponsibleErrorHandlerIfErrorReportingDisabled()
    {
        $oldLevel = error_reporting(0);
        try {
            $this->errorHandler1->mapCalls(['isResponsible' => false]);
            $this->errorHandler2->mapCalls(
                    ['isResponsible' => true,
                     'isSupressable' => false,
                     'handle'        => ErrorHandler::STOP_ERROR_HANDLING
                    ]
            );
            assert(
                    $this->errorHandlers->handle(1, 'foo'),
                    equals(ErrorHandler::STOP_ERROR_HANDLING)
            );
            verify($this->errorHandler3, 'isResponsible')->wasNeverCalled();
        } finally {
            error_reporting($oldLevel);
        }
    }

    /**
     * @test
     */
    public function handleSignalsResultOfResponsibleErrorHandlerIfErrorReportingEnabled()
    {
        $oldLevel = error_reporting(E_ALL);
        try {
            $this->errorHandler1->mapCalls(['isResponsible' => false]);
            $this->errorHandler2->mapCalls(
                    ['isResponsible' => true,
                     'isSupressable' => false,
                     'handle'        => ErrorHandler::STOP_ERROR_HANDLING
                    ]
            );
            assert(
                    $this->errorHandlers->handle(1, 'foo'),
                    equals(ErrorHandler::STOP_ERROR_HANDLING)
            );
        } finally {
            error_reporting($oldLevel);
        }
    }
}
