<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments;
use bovigo\callmap\NewInstance;
use stubbles\Environment;
use stubbles\environments\errorhandler\ErrorHandler;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Helper class for the test.
 */
abstract class ErrorHandlerEnvironment implements Environment
{
    use Handler;

    /**
     * sets the error handler to given class and method name
     *
     * To register the new error handler call registerErrorHandler().
     *
     * @param   string|object  $class        name or instance of error handler class
     * @param   string         $methodName   name of error handler method
     * @return  \stubbles\Environment
     */
    public function useErrorHandler($class, $methodName = 'handle')
    {
        return $this->setErrorHandler($class, $methodName);
    }
}
/**
 * Tests for stubbles\environments\Handler.
 *
 * Contains all tests which require restoring the previous error handler.
 *
 * @group  environments
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\Environment
     */
    protected $environment;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->environment = NewInstance::of(ErrorHandlerEnvironment::class);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function registerErrorHandlerWithInvalidClassThrowsIllegalArgumentException()
    {
        $this->environment->useErrorHandler(404);
    }

    /**
     * @test
     */
    public function registerErrorHandlerWithClassNameReturnsCreatedInstance()
    {
        $errorHandlerClass = NewInstance::classname(ErrorHandler::class);
        assert(
                $this->environment->useErrorHandler($errorHandlerClass)
                        ->registerErrorHandler('/tmp'),
                isInstanceOf($errorHandlerClass)
        );
    }

    /**
     * @test
     */
    public function registerErrorHandlerWithInstanceReturnsGivenInstance()
    {
        $errorHandler = NewInstance::of(ErrorHandler::class);
        assert(
                $this->environment->useErrorHandler($errorHandler)
                        ->registerErrorHandler('/tmp'),
                isSameAs($errorHandler)
        );
    }
}
