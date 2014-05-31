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
 * Tests for nstubbles\lang\errorhandler\IllegalArgumentErrorHandler
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class IllegalArgumentErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IllegalArgumentErrorHandler
     */
    protected $illegalArgumentErrorHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->illegalArgumentErrorHandler = new IllegalArgumentErrorHandler();
    }

    /**
     * @test
     */
    public function isNotResponsibleForNonRecoverableErrors()
    {
        $this->assertFalse($this->illegalArgumentErrorHandler->isResponsible(E_NOTICE, 'foo'));
    }

    /**
     * @test
     */
    public function isNotResponsibleForAllRecoverableErrors()
    {
        $this->assertFalse($this->illegalArgumentErrorHandler->isResponsible(E_RECOVERABLE_ERROR, 'foo'));
    }

    /**
     * @test
     */
    public function isResponsibleForRecoverableErrorsWithArgumentPassingErrorMessage()
    {
        $this->assertTrue($this->illegalArgumentErrorHandler->isResponsible(E_RECOVERABLE_ERROR, 'Argument 1 passed to \\some\\package\\Class::method() must be an instance of other\\package\\AnotherClass, string given'));
    }

    /**
     * @test
     */
    public function illegalArgumentsAreNeverSuppressable()
    {
        $this->assertFalse($this->illegalArgumentErrorHandler->isSupressable(E_RECOVERABLE_ERROR, 'foo'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function handleThrowsIllegalArgumentException()
    {
        $this->illegalArgumentErrorHandler->handle(E_RECOVERABLE_ERROR, 'foo');
    }
}
