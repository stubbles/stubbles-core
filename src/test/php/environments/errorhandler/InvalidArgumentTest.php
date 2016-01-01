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
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\environments\errorhandler\InvalidArgument
 *
 * @group  environments
 * @group  environments_errorhandler
 */
class InvalidArgumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\environments\errorhandler\InvalidArgument
     */
    protected $invalidArgument;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->invalidArgument = new InvalidArgument();
    }

    /**
     * @test
     */
    public function isNotResponsibleForNonRecoverableErrors()
    {
        assertFalse($this->invalidArgument->isResponsible(E_NOTICE, 'foo'));
    }

    /**
     * @test
     */
    public function isNotResponsibleForAllRecoverableErrors()
    {
        assertFalse($this->invalidArgument->isResponsible(E_RECOVERABLE_ERROR, 'foo'));
    }

    /**
     * @test
     */
    public function isResponsibleForRecoverableErrorsWithArgumentPassingErrorMessage()
    {
        assertTrue($this->invalidArgument->isResponsible(
                E_RECOVERABLE_ERROR,
                'Argument 1 passed to \\some\\package\\Class::method() must be'
                . ' an instance of other\\package\\AnotherClass, string given'
        ));
    }

    /**
     * @test
     */
    public function illegalArgumentsAreNeverSuppressable()
    {
        assertFalse($this->invalidArgument->isSupressable(E_RECOVERABLE_ERROR, 'foo'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function handleThrowsIllegalArgumentException()
    {
        $this->invalidArgument->handle(E_RECOVERABLE_ERROR, 'foo');
    }
}
