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
use function stubbles\lang\extractObjectProperties;
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
     * @test
     */
    public function addsIllegalArgumentErrorHandler()
    {
        $properties = extractObjectProperties($this->defaultErrorHandler);
        assert(
                $properties['errorHandlers'][0],
                isInstanceOf(InvalidArgument::class)
        );
    }

    /**
     * @test
     */
    public function addsLogErrorHandler()
    {
        $properties = extractObjectProperties($this->defaultErrorHandler);
        assert(
                $properties['errorHandlers'][1],
                isInstanceOf(LogErrorHandler::class)
        );
    }
}
