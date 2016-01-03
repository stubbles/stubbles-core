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
use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Tests for stubbles\environments\errorhandler\DefaultErrorHandler.
 *
 * @group  environments
 * @group  environments_errorhandler
 */
class DefaultErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\environments\errorhandler\DefaultErrorHandler
     */
    private $defaultErrorHandler;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultErrorHandler = new DefaultErrorHandler('/tmp');
    }

    /**
     * retrieves error handler from inside of collection
     *
     * @param   int  $position
     * @return  \stubbles\environments\errorhandler\ErrorHandlers
     */
    private function retrieveHandler($position)
    {
        $handlers = new \ReflectionProperty(ErrorHandlers::class, 'errorHandlers');
        $handlers->setAccessible(true);
        return $handlers->getValue($this->defaultErrorHandler)[$position];
    }

    /**
     * @test
     */
    public function addsIllegalArgumentErrorHandler()
    {
        assert(
                $this->retrieveHandler(0),
                isInstanceOf(InvalidArgument::class)
        );
    }

    /**
     * @test
     */
    public function addsLogErrorHandler()
    {
        assert(
                $this->retrieveHandler(1),
                isInstanceOf(LogErrorHandler::class)
        );
    }
}
