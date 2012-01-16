<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\errorhandler;
/**
 * Tests for net\stubbles\lang\errorhandler\DefaultErrorHandler.
 */
class DefaultErrorHandlerAccessor extends DefaultErrorHandler
{
    /**
     * accesses internal list of added errror handlers
     *
     * @param   DefaultErrorHandler $handler
     * @return  ErrorHandler[]
     */
    public static function getErrorHandlers(DefaultErrorHandler $handler)
    {
        return $handler->errorHandlers;
    }
}
/**
 * Tests for net\stubbles\lang\errorhandler\DefaultErrorHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class DefaultErrorHandlerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DefaultErrorHandler
     */
    protected $defaultErrorHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultErrorHandler = new DefaultErrorHandler('/tmp');
    }

    /**
     * @test
     */
    public function hasAddedAllErrorHandlers()
    {
        $errorHandlers = DefaultErrorHandlerAccessor::getErrorHandlers($this->defaultErrorHandler);
        $this->assertInstanceOf('net\\stubbles\\lang\\errorhandler\\IllegalArgumentErrorHandler',
                                $errorHandlers[0]
        );
        $this->assertInstanceOf('net\\stubbles\\lang\\errorhandler\\LogErrorHandler',
                                $errorHandlers[1]
        );
    }
}
?>